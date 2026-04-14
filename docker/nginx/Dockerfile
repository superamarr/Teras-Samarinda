# --- Build Vue (production) ---
FROM node:22-alpine AS frontend-build
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci

COPY . .
ARG VITE_API_BASE_URL=
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
RUN npm run build

# --- Nginx untuk static + reverse proxy ke PHP ---
FROM nginx:1.27-alpine

COPY --from=frontend-build /app/dist /usr/share/nginx/html
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www/backend/public/uploads \
    && chown -R nginx:nginx /var/www/backend/public/uploads

EXPOSE 80
