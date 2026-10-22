package main

import (
	"fmt"
	"net/http"
)

func main() {
	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "API ultra-légère.")
	})
	fmt.Println("Serveur Go en écoute sur 8080...")
	http.ListenAndServe(":8080", nil)
}
