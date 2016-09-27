package models

import (
	"tmac/lib"
	"os"
	"fmt"
)

var Module_images map[string] []string

func InitSource() {	
	var err error
	baseDir, err := os.Getwd()
	if err != nil {
    		fmt.Println("get current directory error=%s\n", err)
	}
	
	imageResourceDir := fmt.Sprintf("%s/resource/dongtai/images/", baseDir)	
	Module_images = LoadImageResource(imageResourceDir)
	//fmt.Println( Module_images)
}


func LoadImageResource(imageResourceDir string) ( map[string] []string){
	imageDirs, _ := lib.ListDir(imageResourceDir)
	var dir_files map[string] []string
	dir_files = make(map[string] []string)
	var files []string
	for _, imageDir := range imageDirs {
        	files, _ = lib.ListFile(imageResourceDir + imageDir, "")
		dir_files[imageDir] = files
	}
	return dir_files
}

