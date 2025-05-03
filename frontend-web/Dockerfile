FROM node:20

WORKDIR /app
COPY . .

RUN npm install
RUN npm run build

# Serve static build
RUN npm install -g serve
CMD ["serve", "-s", "dist", "-l", "3000"]