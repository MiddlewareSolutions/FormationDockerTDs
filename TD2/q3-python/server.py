from http.server import BaseHTTPRequestHandler, HTTPServer

class MonServeur(BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/health':
            self.send_response(200)
            self.send_header('Content-type', 'text/plain')
            self.end_headers()
            self.wfile.write(b"Tout va bien !")
        else:
            self.send_response(404)
            self.end_headers()

print("Serveur Python démarré sur le port 8000...\n")
print("")
HTTPServer(('', 8000), MonServeur).serve_forever()
