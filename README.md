# Privremena-regulacija---Waze
Aplikacija Grada Zagreba za objavu privremene regulacije (zatvorenih prometnica) na platformi Waze

### Zahtjevi:
- PHP poslužitelj (testirano na Apache 2.4.62 i PHP 8.3.13)
- MySQL baza podataka (testirano na 8.0.40)
- Waze for cities račun s korisničkim podacima za prijavu te tokenom za reverse geocoding
- u Waze for cities platformi postavljen API za dohvat podataka s vaše aplikacije - URL do home/admin/api/waze.php
- aplikacija koristi besplatnu inačicu Sneat html predloška - obavezno proučiti i pridržavati se licence čiji se uvjeti nalaze na https://themewagon.com/license/


### Instalacija/postavljanje aplikacije:
- u home/root direktorij PHP/Apache poslužitelja klonirajte sadržaj repozitorija
- kreirajte proizvoljnu bazu podataka i napravite restore/import baze iz datoteke "baza.sql"
- u datoteci "config.inc.php" podesite tražene parametre za spajanje na bazu podataka, povezivanje s Waze platformom i sl.

### Korištenje aplikacije:
- pristupite aplikaciji putem preglednika
- za prijavu u aplikaciju koristite sljedće administracijske podatke:
	- korisničko ime: admin
	- lozinka: admin
- nakon prve prijave, promijenite admin lozinku (klikom na ikonicu profila u gornjem desnom kutu ekrana i odabirom opcije "Promjena lozinke")
- nakon prijave u aplikaciju kreirajete proizvoljan broj korisničkih računa, ako je potrebno
- možete početi objavljivati zatvorene prometnice na Waze platformi
