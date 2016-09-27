package lib

import (
	"reflect"
	"fmt"
	"io"
	"time"
	"tmac/common"
	"strconv" 
	"io/ioutil"
	"os"
	"strings"
	"path"
	"encoding/base64"
)

	
func Call(m map[string]interface{}, name string, params ... interface{}) (result []reflect.Value) {
    
	f := reflect.ValueOf(m[name])
	in := make([]reflect.Value, len(params))
	for k, param := range params {
		in[k] = reflect.ValueOf(param)
	}
	result = f.Call(in)
	return
}

func ComputeDtPushKey(dtid int64,timestamp int64) (key string) {
	datingid := strconv.FormatInt(dtid,10)
	timestampStr := strconv.FormatInt(timestamp,10)
	return strconv.Itoa(common.DATINGTALK) + datingid + timestampStr
}

func LoadResource(imageDirs []string) {
	var dir_files map[string] []string
	dir_files = make(map[string] []string)
	var files []string
	for _, imageDir := range imageDirs {
        	files, _ = ListFile(imageDir, "")
		//fmt.Println(imageDir)
		//fmt.Println(files)
		dir_files[imageDir] = files
	}
	//fmt.Println(dir_files);
}


//file and directory operate
func ListDir(dirPth string) (files []string, err error) {
	files = make([]string, 0, 10)
	dir, err := ioutil.ReadDir(dirPth)
	if err != nil {
		return nil, err
	}
	for _, fi := range dir {
		if fi.IsDir() { 
            		files = append(files, fi.Name())
		}
	}	
    	return files, nil
}

func ListFile(dirPth string, prefix string) (files []string, err error) {
	files = make([]string, 0, 10)
	dir, err := ioutil.ReadDir(dirPth)
	if err != nil {
		return nil, err
	}
	//PthSep := string(os.PathSeparator)
	prefix = strings.ToUpper(prefix) 
	for _, fi := range dir {
		if fi.IsDir() { 
			continue
		}
        	if strings.HasPrefix(strings.ToUpper(fi.Name()), prefix) { 
			files = append(files, fi.Name())
        	}
    	}
    	return files, nil
}
func GetFileName(filePath string) (filename string) {
    	filenameWithSuffix := path.Base(filePath)
    	fileSuffix := path.Ext(filenameWithSuffix)
    	filename = strings.TrimSuffix(filenameWithSuffix, fileSuffix)
	return
}

func ReadFile(filePath string)(b []byte) {
	br, _ := ioutil.ReadFile(filePath)
	return br 
}

func WriteFile(filePath string, text string) {
	f, err := os.Create(filePath)
	defer f.Close()
	check(err)
	n, err1 := io.WriteString(f, text)
	check(err1)
	fmt.Println("write %d bytes", n)
}


func GetInfoByTicket(ticket string) (tickteinfo []string) {
	stamp_newid := strings.Split(ticket, "_")
	t := stamp_newid[0]
        stamp := t[0:4] + "-" + t[4:6] + "-" + t[6:8] + " " + t[8:10] + ":" + t[10:12] + ":" + t[12:14];
	newid := stamp_newid[1];
	tickteinfo = append(tickteinfo, stamp)
	tickteinfo = append(tickteinfo, newid)
	return tickteinfo;
}
//suanfa fuction 
func GetNextImages(images []string, ticket string, flag string, cnt string) (files []string, err error){ 
	var start int;
	var end int;
	var num int;
	
	stamp_index := strings.Split(ticket, "_") 
	start, _ = strconv.Atoi(stamp_index[1]);
	num, _ = strconv.Atoi(cnt);
	if flag == "0" {
		end = start + num;
		if end > len(images) {
			end = len(images)
		}
	} else {
		end = start -1
		start = end - num
		if start < 0 {
			start = 0;
		}
	}
     	files = images[start:end]
    	return files, nil
}
//other
func EncodeImageBase64(imagePath string) (encodeStr string) {
	ff, _ := ioutil.ReadFile(imagePath)
	fbytes := base64.StdEncoding.EncodeToString(ff)
	return string(fbytes)
}
func Base64Encode(data string) (base64_data string){
	sEnc := base64.StdEncoding.EncodeToString([]byte(data))
	return sEnc
}
func Base64Decode(data string) (base64_data string){
	sDec, _ := base64.StdEncoding.DecodeString(data)
	return string(sDec)
}

func check(e error) {
	if e != nil {
		panic(e)
	}
}

func GetTime(diff int64) (time_str string) {
	timestamp := time.Now().Unix() + diff
        tm := time.Unix(timestamp, 0)
        time_str = tm.Format("2006-01-02 15:04:05")
	return time_str
}

func JudgePasswd(passwd1 string, passwd2 string) (int) {
	var Type int
	Type = common.PASSWDDIFFERROR
	if passwd1 == passwd2 {
		if len(passwd1) < 6 {
			Type = common.PASSWDLENGTHERROR
		} else {
			Type = common.PASSWDOK
		}
	}	
	return Type
}
func MakeTicket(time string, id string) (ticket string){

	time1 := strings.Replace(time, "-", "", 2)
	time2 := strings.Replace(time1, ":", "", 2)
	time3 := strings.Replace(time2, " ", "", 1)
	ticket = time3 + "_" + id
	return ticket
}
