FROM postgres:latest

RUN mkdir -p /docker-entrypoint-initdb.d
ADD ./create_r8m3.sql /docker-entrypoint-initdb.d/create_r8m3.sql
