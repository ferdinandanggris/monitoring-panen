FROM node:18

WORKDIR /app
COPY . .

ENV NODE_ENV=development
RUN npm install
RUN chmod +x node_modules/.bin/vite \
    && chmod +x node_modules/@esbuild/linux-x64/bin/esbuild
RUN npm run build

RUN npm install -g serve
CMD ["serve", "-s", "dist", "-l", "3000"]
