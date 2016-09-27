package controllers 
import (
	"fmt"
	"github.com/astaxie/beego"
	_"strings"
        _"tmac/common"
        "tmac/models"
        _"tmac/lib"
        _"encoding/json"
        _"strconv"
        _"github.com/astaxie/beego/cache"
        _"github.com/astaxie/beego/cache/redis" 
        "log" 
        "encoding/json" 
        "strconv" 
        _"time"
	"os"
)
//var imageServer string
//var platformServer string
var leagueLogoDir string
var leaguePosterDir string
func init() {	
	//serverip := beego.AppConfig.String("serverip")
	//image_server_port := beego.AppConfig.String("image_server_port")
	//platform_server_port := beego.AppConfig.String("httpport")

	
	//imageServer = "http://" + serverip + ":" + image_server_port;
	//platformServer = "http://" + serverip + ":" + platform_server_port;
	var err error
	baseDir, err = os.Getwd()
	fmt.Println(baseDir)
	if err != nil {
    		fmt.Println("get current directory error=%s\n", err)
	}

	leagueLogoDir = fmt.Sprintf("%s/%s", baseDir, beego.AppConfig.String("leagueLogoDir"))
	leaguePosterDir = fmt.Sprintf("%s/%s", baseDir, beego.AppConfig.String("leaguePosterDir"))
}

func (this *LeagueController) Prepare() {
}

func (c *LeagueController) Test() {
	module := c.Ctx.Input.Param(":module")
        flag := c.Ctx.Input.Param(":bbb")
     	fmt.Println(module)
     	fmt.Println(flag)
	
}
func (c *LeagueController) ReqinSchoolLeagueCells(){
	schoolid := c.GetString("schoolid")
        num := c.GetString("num")
       	ticket := c.GetString("ticket")
     	fmt.Println(num)
     	fmt.Println(schoolid)
     	fmt.Println(ticket)

	mydb := models.GetMysqlInstance()
	db := mydb.GetDb()
	qSql := "select * from league where schoolid = ? and id > ? limit " + num
	rows, err := db.Query(qSql, schoolid, ticket)
	if err != nil {
		log.Println(err)
	}
 
	defer rows.Close()
	var id int
	var leagueid int
	var logoid int
	var posterid int
	var name string
	var team_num int
	var team_fans int
	var league_type string
	var create_time string
	var start_time string
	var end_time string
	cells := []map[string]string{}
	for rows.Next() {
	 	cell := map[string]string{}
		err := rows.Scan(&id, &schoolid, &leagueid,  &logoid, &posterid, &name, &team_num, &team_fans, &league_type, &create_time, &start_time, &end_time)
		if err != nil {
			log.Fatal(err)
		}
		
		cell["league_id"] =  strconv.Itoa(leagueid)
		cell["league_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + strconv.Itoa(logoid) + ".jpg"
		cell["league_poster_address"] = imageServer + "/" + beego.AppConfig.String("leaguePosterDir") + "/" + strconv.Itoa(posterid) + ".jpg"
		cell["league_name"] =  name 
		cell["league_team_num"] = strconv.Itoa(team_num)
		cell["league_fans_num"] = strconv.Itoa(team_fans)
		cell["league_type"] = league_type
		cell["league_start_date"] = start_time
		cell["league_end_date"] = end_time
		cell["ticket"] = strconv.Itoa(id)
		cells = append(cells, cell)	
		//fmt.Println(id, schoolid, leagueid)
	}
        
	b, err := json.Marshal(cells)
        if err != nil {
                fmt.Println("error", err)
        }

	c.Ctx.WriteString(string(b));
}

func (c *LeagueController) ReqinSchoolLeagueCell(){
	schoolid0 := c.GetString("schoolid")
       	leagueid0 := c.GetString("leagueid")

	schoolid, _ := strconv.Atoi(schoolid0);
	leagueid, _ := strconv.Atoi(leagueid0);

	matches := models.GetMatchInfo(schoolid, leagueid)
	cells := []map[string]string{}
        for _, match := range matches {
	 	cell := map[string]string{}
		cell["matchid"] = match["matchid"]
		cell["match_time"] = match["match_time"]
		cell["match_address"] = match["match_address"]
		cell["teamid1"] = match["teamid1"]
		cell["teamid2"] = match["teamid2"]
		cell["match_time"] = match["match_time"]
		cell["match_address"] = match["match_address"]
		cell["is_end"] = match["status"]

		team_cells := []map[string]string{}
	 	team_cell := map[string]string{}

		teamid1, _ := strconv.Atoi(match["teamid1"]);
		teamInfo := models.GetTeamInfo(teamid1)
		team_cell["team_id"] = teamInfo["team_id"]
		team_cell["team_name"] = teamInfo["team_name"]
		team_cell["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
		team_cell["is_home_team"] = "1"
		team_cells = append(team_cells,team_cell)
		
	 	team_cell1 := map[string]string{}
		teamid2, _ := strconv.Atoi(match["teamid2"]);
		teamInfo = models.GetTeamInfo(teamid2)
		team_cell1["team_id"] = teamInfo["team_id"]
		team_cell1["team_name"] = teamInfo["team_name"]
		team_cell1["team_logo_address"] = imageServer + "/" + beego.AppConfig.String("leagueLogoDir") + "/" + teamInfo["logoid"] + ".jpg"
		team_cell1["is_home_team"] = "0"
		team_cells = append(team_cells,team_cell1)
        	teams, err := json.Marshal(team_cells)
        	if err != nil {
                	fmt.Println("error", err)
		}
		cell["matches"] = string(teams)
		cells = append(cells, cell)	
        }

        b, err := json.Marshal(cells)
        if err != nil {
                fmt.Println("error", err)
        }

	c.Ctx.WriteString(string(b));
}

func (c *LeagueController)ReqinSchoolMatchResult(){
     	fmt.Println("-----------")
	matchid := c.GetString("matchid")
     	fmt.Println(matchid)
	cells := models.GetMatchResultInfo(matchid);
	        
	b, err := json.Marshal(cells)
        if err != nil {
                fmt.Println("error", err)
        }
	
	c.Ctx.WriteString(string(b));
	//c.Ctx.WriteString("helloworld");
}


