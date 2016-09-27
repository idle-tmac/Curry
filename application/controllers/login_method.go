package controllers 
import (
	"fmt"
	_"github.com/astaxie/beego"
	_"strings"
        "tmac/common"
        "tmac/models"
        "tmac/lib"
        _"encoding/json"
        "strconv"
        _"github.com/astaxie/beego/cache"
        _"github.com/astaxie/beego/cache/redis" 
        "log" 
        "encoding/json" 
        _"time"
)
func init() {	
}

func (c *LoginController) LoginCheck(){
	loginway := c.GetString("loginway")
	loginvalue := c.GetString("loginid")
        passwd := c.GetString("passwd")
	//loginway := c.Ctx.Input.Param(":loginway")
	//loginvalue := c.Ctx.Input.Param(":value")
        //passwd := c.Ctx.Input.Param(":passwd")
     	fmt.Println(loginvalue)
     	fmt.Println(passwd)

	mydb := models.GetMysqlInstance()
	db := mydb.GetDb()
	qSql := fmt.Sprintf("select passwd,schoolno from user where %s = ?", loginway)
	fmt.Println(qSql)
	rows, err := db.Query(qSql, loginvalue)
	if err != nil {
		log.Println(err)
	}
 
	defer rows.Close()
	var passwdb string
	var schoolno string
	var isUser bool
	isUser = false
	for rows.Next() {
		isUser = true
		err := rows.Scan(&passwdb, &schoolno)
		if err != nil {
			log.Fatal(err)
		}
	}
       
	cell := map[string]string{}
	cell["type"] = strconv.Itoa(common.USERNOEXIST)
	if isUser {
		cell["type"] = strconv.Itoa(common.USERWRONGPASSWD)
		if (lib.Base64Encode(passwd) == lib.Base64Encode(passwdb)) {
			cell["type"] = strconv.Itoa(common.USERLESSINFO)
			cell["schoolid"] = schoolno
			if schoolno != "" {
				cell["type"] = strconv.Itoa(common.USERPERFECT)
			}
		}
	}
	
	b, err := json.Marshal(cell)
        if err != nil {
                fmt.Println("error", err)
        }

	c.Ctx.WriteString(string(b));
}

