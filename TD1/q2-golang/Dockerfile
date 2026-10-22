FROM golang:1.21
WORKDIR /src

COPY main.go .

# Compilation statique
RUN CGO_ENABLED=0 go build -o /bin/api main.go

CMD ["/bin/api"]
