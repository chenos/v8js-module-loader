install:
	docker-compose run --rm php composer install

test: install
	# docker-compose run --rm node bash -c "cd ./tests/javascript && npm install"
	docker-compose run --rm php composer test-coverage
	docker-compose run --rm php composer coveralls

example: install
	docker-compose run --rm node bash -c "cd ./examples && npm install"
	docker-compose up -d nginx
