# advance networking assignment documents

## related to question one

- the docker fies include inside each service project and one single docker-compose file is created to in the root of the `web` dir which contains the `docker-compose` needed to start the services
- in the `web/nginx` folder we have the nginx configuration we have used

```nginx
location /products {
    rewrite ^/products(.*)$ $1 break;
    proxy_pass http://products;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
}
```

- as here ew are using reverse proxy to redirect to relevent services based on the path, and we also include the ip addr in `X-Real-IP` header

## related to question two

- for the question two the setting up opendaylight [here](https://www.brianlinkletter.com/2016/02/using-the-opendaylight-sdn-controller-with-the-mininet-network-emulator/) follow this url
- the mininet was installed using apt package manager check the [question-two/install.sh](question-two/install.sh) for details
- as for the configration of opendaylight we need to figure out an way to configure opendaylight in some way it seems to have an tty so dont think its possible to script it to cauto configure so need to figure out if they have some type of api or sdk to figure our if we can automate that script
- the `question-two` dir contains the related scripts for all the configs.
