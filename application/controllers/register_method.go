package controllers 
import (
	"fmt"
	_"github.com/astaxie/beego"
	_"strings"
        "tmac/common"
        "tmac/models"
        "tmac/lib"
        "strconv"
        _"github.com/astaxie/beego/cache"
        _"github.com/astaxie/beego/cache/redis" 
        _"log" 
        "encoding/json" 
        _"time"
)

var phoneVc  map[string]string

func init() {	
	phoneVc = make(map[string]string)
}

func (c *RegisterController) RegisterDeal(){
	phone := c.GetString("phone")
	vc := c.GetString("verifycode")
	//phone := c.Ctx.Input.Param(":phone")
	//vc := c.Ctx.Input.Param(":verifycode")
	

	/*
	mydb := models.GetMysqlInstance()
	db := mydb.GetDb()
	qSql := fmt.Sprintf("select userid from user where phone = ?")
	fmt.Println(qSql)
	rows, err := db.Query(qSql, phone)
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
	*/
     	fmt.Println(phone)
     	fmt.Println(vc)
	retRegInfo := map[string]string{}
	if vc == "0" {
		// generate random verify code 
		newVerifyCode = "123456"
		phoneVc[phone] = newVerifyCode	
	} else {
		retRegInfo["ret"] = "0"
		if vc == phoneVc[phone] {
			retRegInfo["ret"] = "1"
		}
	}
	fmt.Println(retRegInfo) 
	b, err := json.Marshal(retRegInfo)
        if err != nil {
                fmt.Println("error", err)
        }

	c.Ctx.WriteString(string(b));
}

func (c *RegisterController) RegisterPasswdUpLoad(){
	phone := c.GetString("phone")
	passwd1 := c.GetString("passwd1")
	passwd2 := c.GetString("passwd2")
	//phone := c.Ctx.Input.Param(":phone")
	//passwd1 := c.Ctx.Input.Param(":passwd1")
	//passwd2 := c.Ctx.Input.Param(":passwd2")
     	fmt.Println(phone)
     	fmt.Println(passwd1)
     	fmt.Println(passwd2)
	cell := map[string]string{}
	ret := lib.JudgePasswd(passwd1, passwd2)
	cell["type"] = strconv.Itoa(ret)

	var dbret bool
	dbret = false
	if ret == common.PASSWDOK {
		time_str := lib.GetTime(0) ;   
		fmt.Println(time_str)
		inSql := "insert into user(phone, passwd, create_time) values (?, ?,?)"
		dbret = models.GetMysqlInstance().Insert(inSql, phone, passwd1, time_str) 
		if !dbret {
			cell["type"] = strconv.Itoa(common.PASSWDINSERTERROR)
		}
	}

	b, err := json.Marshal(cell)
        if err != nil {
                fmt.Println("error", err)
        }
	c.Ctx.WriteString(string(b));
}
