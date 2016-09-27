
package models
  
import (
	_"fmt"
    "github.com/astaxie/beego/cache"
    _"github.com/astaxie/beego/cache/redis"
	"log"
)
  
var redisBase *RedisBase
  
type RedisBase struct {
	RedisInst cache.Cache
}
  
func GetRedisInstance() cache.Cache {
	if redisBase == nil {
		redisInst, err := cache.NewCache("redis", `{"conn":"127.0.0.1:6379", "key":"beecacheRedis"}`);
        if err != nil {
            log.Println(err)
        }
	    redisBase = &RedisBase{RedisInst:redisInst}
   	}
   	return redisBase.RedisInst;
}

