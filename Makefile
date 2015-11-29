#
# Populates the account with relevant files
#

all: .ssh/authorized_keys data/acquisitariat.dat

.ssh/authorized_keys:
	mkdir -p -m 0700 .ssh
	php bin/getpublickeys > .ssh/authorized_keys
	chmod 600 .ssh/authorized_keys

data/acquisitariat.dat:
	openssl rand -hex 21 > data/acquisitariat.dat

