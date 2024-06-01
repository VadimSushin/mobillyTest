# syntax=docker/dockerfile:1

FROM php:8.2-apache

LABEL authors="Vadim Sushin"

RUN a2enmod rewrite
COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY . /var/www/html
RUN rm -rf /var/www/html/var/*

WORKDIR /var/www/html
