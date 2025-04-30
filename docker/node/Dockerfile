FROM node:18

WORKDIR /usr/src/app

COPY ./node-server/package*.json ./

RUN npm install

COPY ./node-server .

EXPOSE 3000

CMD ["npm", "start"]