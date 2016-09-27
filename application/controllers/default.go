package controllers

import (
	"github.com/astaxie/beego"
        _"fmt"
        _"encoding/json"
)

type MainController struct {
	beego.Controller
}

func (c *MainController) Get() {
	c.Data["Website"] = "beego.me"
	c.Data["Email"] = "astaxie@gmail.com"
	c.TplName = "index.tpl"
        /*homeInfo := map[string]string{} 
        homeInfo["brother"] = "basketball"
        b, err := json.Marshal(homeInfo)
        if err != nil {
                fmt.Println("error", err)
        }
        c.Ctx.WriteString(string(b)) */
}
