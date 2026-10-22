FROM python:3.11-alpine
WORKDIR /app
COPY server.py .

RUN apk add --no-cache wget

# Instruction de Health-check : Docker va executer 'wget' toutes les 5 secondes
HEALTHCHECK --interval=5s --timeout=3s --retries=3 \
  CMD wget -qO- http://localhost:8000/health || exit 1

CMD ["python", "server.py"]