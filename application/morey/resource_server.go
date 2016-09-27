package main
import (
	_"os"
	"fmt"
	"net/http"
	_"path/filepath"
)
func main() {
    //http.HandleFunc("/resource/dongtai/images/recommend/", GetResourceImage)
    //http.HandleFunc("/resource/league/logo/", GetResourceLogo)
    http.HandleFunc("/resource/", GetResource)
    http.ListenAndServe(":8080", nil)
}
 
func GetResource(w http.ResponseWriter, r *http.Request) {
    path1 := r.URL.String()
    fmt.Println(path1);
    path := "/home/tmac/project/src/tmac" + path1;
    fmt.Println(path);
    http.ServeFile(w, r, path)
}

/*
func GetResourceImage(w http.ResponseWriter, r *http.Request) {
    path1 := r.URL.String()
    fmt.Println(path1);
    path := "/home/tmac/project/src/tmac" + path1;
    fmt.Println(path);
    http.ServeFile(w, r, path)
}

func GetResourceLogo(w http.ResponseWriter, r *http.Request) {
    path1 := r.URL.String()
    fmt.Println(path1);
    path := "/home/tmac/project/src/tmac" + path1;
    fmt.Println(path);
    http.ServeFile(w, r, path)
}*/
