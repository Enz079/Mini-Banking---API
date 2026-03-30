# Esercitazione: Mini Banking API

## Obiettivo
## Scenario

Progettare e sviluppare un backend REST che simuli un **conto bancario semplificato**.
Dovete realizzare un backend REST che simuli un **conto bancario semplificato**.

L'applicazione deve permettere di:
Non serve alcun frontend: il progetto deve esporre endpoint HTTP che restituiscono **JSON**.

- registrare **depositi**
- registrare **prelievi**
- visualizzare la **lista dei movimenti**
- visualizzare il **dettaglio di un movimento**
- calcolare il **saldo attuale**
- convertire il saldo in un'altra valuta usando una **API esterna pubblica**
---

## Obiettivo finale

Non è richiesto alcun frontend.
Alla fine dell'esercitazione l'applicazione deve permettere di:

1. registrare **depositi**
2. registrare **prelievi**
3. visualizzare la **lista dei movimenti**
4. visualizzare il **dettaglio di un movimento**
5. calcolare il **saldo attuale**
6. convertire il saldo in una **valuta fiat** usando **Frankfurter**
7. convertire il saldo in una **criptovaluta** usando **Binance**

---

## Modalità di lavoro

- Lavoro a gruppi di **2 o 3 studenti**
- Durata: **3 ore**
- Il progetto deve usare:
- gruppi di **2 o 3 studenti**
- tecnologie richieste:
  - **Slim**
  - **database MySQL/MariaDB**
  - **MySQL** oppure **MariaDB**
  - risposte in formato **JSON**

---

## Requisiti minimi
## Impostazione consigliata

Per rendere il lavoro più lineare:

Il backend deve esporre endpoint con metodi:
- potete gestire anche **un solo conto**
- è consigliato usare, per la parte obbligatoria, **un conto in EUR**
- potete comunque mantenere nel database il campo `currency`

Questa scelta semplifica soprattutto la parte di conversione crypto.

---

- `GET`
- `POST`
- `PUT`
- `DELETE`
## Percorso di lavoro consigliato

Il progetto deve includere:
Per non bloccarvi, affrontate il progetto in questo ordine:

1. almeno **una tabella** nel database
2. almeno **una integrazione HTTP** con una API esterna
3. validazione degli input
4. gestione degli errori con codici HTTP corretti
5. output JSON chiaro e coerente
1. create database e tabelle
2. realizzate depositi e prelievi
3. calcolate il saldo
4. aggiungete la lista e il dettaglio dei movimenti
5. aggiungete la conversione in valuta fiat con Frankfurter
6. aggiungete la conversione in crypto con Binance
7. completate validazioni, gestione errori e pulizia delle risposte JSON

---

## Modello dati suggerito

### Tabella `accounts`

Per semplicità potete usare anche **un solo conto**.

Campi suggeriti:
Campi minimi consigliati:

- `id`
- `owner_name`
@@ -62,7 +71,7 @@ Campi suggeriti:

### Tabella `transactions`

Campi suggeriti:
Campi minimi consigliati:

- `id`
- `account_id`
@@ -71,13 +80,13 @@ Campi suggeriti:
- `description`
- `created_at`

### Campo opzionale consigliato
### Campo opzionale utile

Potete aggiungere anche:

- `balance_after`

Questo campo salva il saldo risultante dopo ogni operazione.
Questo campo salva il saldo risultante dopo ogni operazione e può aiutarvi nei controlli.

---

@@ -94,178 +103,167 @@ Questo campo salva il saldo risultante dopo ogni operazione.

### Saldo

Il saldo non deve essere inserito manualmente.
Il saldo **non** deve essere inserito manualmente.

Può essere calcolato come:
Deve essere calcolato come:

- somma dei depositi
- meno somma dei prelievi

### Modifica dei movimenti

Per evitare incoerenze, è consigliato permettere con `PUT` solo la modifica di campi come:
Per evitare incoerenze, è consigliato permettere con `PUT` solo la modifica di campi descrittivi, ad esempio:

- `description`

### Eliminazione dei movimenti

Potete definire una regola, ad esempio:

- è possibile eliminare solo l'ultimo movimento
- oppure è possibile eliminare un movimento solo se il saldo finale rimane valido

---

## Endpoint di esempio

### Elenco movimenti

`GET /accounts/1/transactions`
Potete scegliere una regola semplice ma coerente, ad esempio:

### Dettaglio movimento
- si può eliminare solo l'ultimo movimento
- oppure si può eliminare un movimento solo se il saldo finale rimane valido

`GET /accounts/1/transactions/5`
L'importante è che la regola sia chiara e rispettata dal codice.

### Saldo attuale

`GET /accounts/1/balance`
---

### Conversione del saldo in un'altra valuta
## Endpoint richiesti

`GET /accounts/1/balance/convert?to=USD`
### Movimenti

### Nuovo deposito
- `GET /accounts/1/transactions` per ottenere l'elenco dei movimenti
- `GET /accounts/1/transactions/5` per ottenere il dettaglio di un movimento
- `POST /accounts/1/deposits` per registrare un deposito
- `POST /accounts/1/withdrawals` per registrare un prelievo
- `PUT /accounts/1/transactions/5` per modificare la descrizione di un movimento
- `DELETE /accounts/1/transactions/5` per eliminare un movimento secondo la regola scelta

`POST /accounts/1/deposits`
### Saldo

Body JSON:
- `GET /accounts/1/balance` per ottenere il saldo attuale

```json
{
  "amount": 150.00,
  "description": "Versamento iniziale"
}
```
### Conversione del saldo

### Nuovo prelievo
- `GET /accounts/1/balance/convert/fiat?to=USD` per convertire il saldo in una valuta fiat
- `GET /accounts/1/balance/convert/crypto?to=BTC` per convertire il saldo in una criptovaluta

`POST /accounts/1/withdrawals`
Potete scegliere nomi leggermente diversi per gli endpoint, ma la struttura deve rimanere chiara e coerente.

Body JSON:
---

```json
{
  "amount": 20.00,
  "description": "Prelievo bancomat"
}
```
## Dati minimi da accettare

### Modifica descrizione movimento
### Deposito e prelievo

`PUT /accounts/1/transactions/5`
Nel body JSON devono esserci almeno:

Body JSON:
- `amount`
- `description`

```json
{
  "description": "Prelievo ATM aggiornato"
}
```
### Modifica movimento

### Eliminazione movimento
Nel body JSON è sufficiente:

`DELETE /accounts/1/transactions/5`
- `description`

---

## Esempi di risposte JSON
## Conversione fiat con Frankfurter

### Saldo
Per questa parte dovete:

```json
{
  "account_id": 1,
  "currency": "EUR",
  "balance": 130.00
}
```
1. calcolare il saldo del conto nel database
2. leggere la valuta di partenza del conto
3. leggere il parametro `to`
4. chiedere a Frankfurter il tasso di cambio aggiornato
5. moltiplicare il saldo per il tasso ottenuto
6. restituire il risultato in JSON

### Saldo convertito

```json
{
  "account_id": 1,
  "from_currency": "EUR",
  "to_currency": "USD",
  "original_balance": 130.00,
  "converted_balance": 141.22,
  "rate": 1.0863
}
```
### Regole consigliate

---

## API esterna da consumare

Per la conversione del saldo dovete usare **Frankfurter**, una API pubblica gratuita per tassi di cambio.

### Endpoint utili di Frankfurter
- accettate solo codici valuta realmente supportati
- se il parametro `to` manca o non è valido, restituite errore
- il saldo convertito in valuta fiat può essere arrotondato a **2 decimali**

#### Elenco valute supportate
### Campi utili nella risposta

`GET https://api.frankfurter.dev/v1/currencies`

Restituisce un oggetto JSON con i codici valuta e il nome completo.
- `account_id`
- `provider`
- `conversion_type`
- `from_currency`
- `to_currency`
- `original_balance`
- `rate`
- `converted_balance`
- `date`

#### Tassi più recenti
---

`GET https://api.frankfurter.dev/v1/latest`
## Conversione crypto con Binance

Per default usa `EUR` come valuta base.
Per questa parte dovete:

#### Cambio della valuta base
1. calcolare il saldo del conto nel database
2. leggere il parametro `to`, che rappresenta la crypto richiesta, ad esempio `BTC` o `ETH`
3. costruire la coppia di mercato Binance usando la crypto come base e la valuta del conto come quote
4. verificare che la coppia esista e sia utilizzabile
5. recuperare il prezzo corrente della crypto
6. convertire il saldo del conto nella quantità di crypto corrispondente
7. restituire il risultato in JSON

`GET https://api.frankfurter.dev/v1/latest?base=USD`
### Esempio logico di conversione

Il parametro `base` permette di scegliere la valuta di partenza.
Se il conto è in `EUR` e volete convertire il saldo in `BTC`:

#### Limitare la risposta a valute specifiche
- la coppia di mercato da cercare è `BTCEUR`
- il prezzo indica quanto costa **1 BTC** in EUR
- la quantità di BTC ottenibile si calcola dividendo il saldo in EUR per il prezzo di `BTCEUR`

`GET https://api.frankfurter.dev/v1/latest?base=EUR&symbols=USD,GBP`
### Regole consigliate

Il parametro `symbols` permette di richiedere solo alcune valute target.
- è sufficiente supportare solo le crypto per cui esiste una coppia attiva con la valuta del conto
- se la coppia non esiste, restituite un errore chiaro
- prima di leggere il prezzo, controllate che il simbolo sia presente tra quelli disponibili
- per la quantità crypto potete usare fino a **8 decimali**

### Come usarla nel progetto
### Campi utili nella risposta

Se il conto è in `EUR` e volete convertire il saldo in `USD`:
- `account_id`
- `provider`
- `conversion_type`
- `from_currency`
- `to_crypto`
- `market_symbol`
- `original_balance`
- `price`
- `converted_amount`

1. calcolate il saldo locale dal database
2. chiamate l'endpoint:
---

`GET https://api.frankfurter.dev/v1/latest?base=EUR&symbols=USD`
## Esempio di implementazione in Slim

3. prendete il tasso `USD`
4. moltiplicate il saldo locale per quel tasso
5. restituite una risposta JSON progettata da voi
Di seguito trovate un esempio unico e completo di endpoint Slim per la conversione del saldo in una valuta fiat con Frankfurter.

---
L'esempio mostra insieme:

## Esempio di implementazione in Slim
- lettura del parametro `to`
- recupero del conto dal database
- calcolo del saldo
- chiamata HTTP verso una API esterna
- gestione degli errori principali
- risposta JSON finale

Di seguito un esempio semplice di endpoint Slim che:
Per la parte **crypto con Binance**, invece, dovete costruire voi la chiamata leggendo la documentazione ufficiale e adattando la stessa logica vista qui.

- calcola il saldo di un conto nel database
- legge il parametro `to`
- chiama Frankfurter
- restituisce il saldo convertito
Nello snippet si assume di avere gia una connessione `mysqli` disponibile nella variabile `$mysqli`.

```php
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/accounts/{id}/balance/convert', function (Request $request, Response $response, array $args) use ($pdo) {
$app->get('/accounts/{id}/balance/convert/fiat', function (Request $request, Response $response, array $args) use ($mysqli) {
    $accountId = (int)$args['id'];
    $params = $request->getQueryParams();
    $to = strtoupper($params['to'] ?? '');
@@ -279,10 +277,11 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response
            ->withStatus(400);
    }

    // Recupero conto
    $stmt = $pdo->prepare('SELECT id, currency FROM accounts WHERE id = ?');
    $stmt->execute([$accountId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $mysqli->prepare('SELECT id, currency FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if (!$account) {
        $response->getBody()->write(json_encode([
@@ -295,18 +294,19 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response

    $from = strtoupper($account['currency']);

    // Calcolo saldo: depositi - prelievi
    $stmt = $pdo->prepare("
    $stmt = $mysqli->prepare("
        SELECT
            COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) -
            COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) AS balance
        FROM transactions
        WHERE account_id = ?
    ");
    $stmt->execute([$accountId]);
    $balance = (float)$stmt->fetchColumn();
    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $balance = (float)($row['balance'] ?? 0);

    // Chiamata API esterna
    $url = "https://api.frankfurter.dev/v1/latest?base={$from}&symbols={$to}";
    $json = @file_get_contents($url);

@@ -335,6 +335,8 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response

    $response->getBody()->write(json_encode([
        'account_id' => $accountId,
        'provider' => 'Frankfurter',
        'conversion_type' => 'fiat',
        'from_currency' => $from,
        'to_currency' => $to,
        'original_balance' => $balance,
@@ -347,6 +349,13 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response
});
```

### Come riusare questo schema negli altri endpoint

- `400` se manca un parametro o il client manda un dato non valido
- `404` se il conto o il movimento non esistono
- `422` se il dato è valido come formato ma viola una regola di business, ad esempio un prelievo troppo alto
- `502` se fallisce la chiamata a una API esterna

---

## Errori da gestire
@@ -356,7 +365,9 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response
- importo mancante
- importo non valido
- valuta target mancante
- valuta non supportata
- valuta fiat non supportata
- crypto target non supportata
- coppia Binance non valida

### `404 Not Found`

@@ -369,34 +380,65 @@ $app->get('/accounts/{id}/balance/convert', function (Request $request, Response

### `502 Bad Gateway`

- errore nella chiamata alla API esterna
- errore nella chiamata a Frankfurter
- errore nella chiamata a Binance

---

## Documentazione ufficiale da usare

Per questa esercitazione dovete consultare la documentazione ufficiale dei servizi esterni.

### Frankfurter

- Documentazione generale: [https://frankfurter.dev/](https://frankfurter.dev/)
- Endpoint valuta disponibili: [https://api.frankfurter.dev/v1/currencies](https://api.frankfurter.dev/v1/currencies)
- Endpoint tassi aggiornati: [https://api.frankfurter.dev/v1/latest](https://api.frankfurter.dev/v1/latest)

### Binance Spot API

- Informazioni generali REST: [https://developers.binance.com/docs/binance-spot-api-docs/rest-api](https://developers.binance.com/docs/binance-spot-api-docs/rest-api)
- Endpoint generali, inclusa `exchangeInfo`: [https://developers.binance.com/docs/binance-spot-api-docs/rest-api/general-endpoints](https://developers.binance.com/docs/binance-spot-api-docs/rest-api/general-endpoints)
- Endpoint market data, incluso `ticker/price`: [https://developers.binance.com/docs/binance-spot-api-docs/rest-api/market-data-endpoints](https://developers.binance.com/docs/binance-spot-api-docs/rest-api/market-data-endpoints)

Potete partire dagli snippet di questa traccia e usare la documentazione ufficiale per capire meglio **endpoint**, **parametri**, **risposte** e **vincoli**.

---

## Cosa consegnare

Ogni gruppo deve consegnare:

1. codice del progetto
2. schema del database
3. elenco degli endpoint realizzati
1. il codice del progetto
2. lo schema del database
3. l'elenco degli endpoint realizzati
4. almeno un esempio di chiamata per ogni endpoint
5. breve spiegazione delle scelte progettuali
5. una breve spiegazione delle scelte progettuali

---

## Criteri di valutazione

Saranno valutati soprattutto:

- correttezza degli endpoint REST
- qualità del modello dati
- correttezza della logica di business
- uso corretto del database
- integrazione con API esterna
- integrazione con Frankfurter
- integrazione con Binance
- gestione degli errori
- chiarezza del JSON restituito

---

## Suggerimento finale

È preferibile realizzare **pochi endpoint ma ben fatti**, piuttosto che molti endpoint incompleti.
È meglio realizzare **pochi endpoint ma solidi e coerenti** che molti endpoint incompleti.

Chi completa prima la parte base può migliorare:

- validazioni
- messaggi di errore
- struttura delle risposte JSON
- supporto a più valute o più crypto
benve-meucci created this gist 2 weeks ago.
 402 changes: 402 additions & 0 deletions402  
MiniBankingAPI.md
Original file line number	Diff line number	Diff line change
@@ -0,0 +1,402 @@
# Esercitazione: Mini Banking API

## Obiettivo

Progettare e sviluppare un backend REST che simuli un **conto bancario semplificato**.

L'applicazione deve permettere di:

- registrare **depositi**
- registrare **prelievi**
- visualizzare la **lista dei movimenti**
- visualizzare il **dettaglio di un movimento**
- calcolare il **saldo attuale**
- convertire il saldo in un'altra valuta usando una **API esterna pubblica**

Non è richiesto alcun frontend.

---

## Modalità di lavoro

- Lavoro a gruppi di **2 o 3 studenti**
- Durata: **3 ore**
- Il progetto deve usare:
  - **Slim**
  - **database MySQL/MariaDB**
  - risposte in formato **JSON**

---

## Requisiti minimi

Il backend deve esporre endpoint con metodi:

- `GET`
- `POST`
- `PUT`
- `DELETE`

Il progetto deve includere:

1. almeno **una tabella** nel database
2. almeno **una integrazione HTTP** con una API esterna
3. validazione degli input
4. gestione degli errori con codici HTTP corretti
5. output JSON chiaro e coerente

---

## Modello dati suggerito

### Tabella `accounts`

Per semplicità potete usare anche **un solo conto**.

Campi suggeriti:

- `id`
- `owner_name`
- `currency`
- `created_at`

### Tabella `transactions`

Campi suggeriti:

- `id`
- `account_id`
- `type` (`deposit` oppure `withdrawal`)
- `amount`
- `description`
- `created_at`

### Campo opzionale consigliato

Potete aggiungere anche:

- `balance_after`

Questo campo salva il saldo risultante dopo ogni operazione.

---

## Regole di business

### Deposito

- l'importo deve essere maggiore di zero

### Prelievo

- l'importo deve essere maggiore di zero
- non si può prelevare più del saldo disponibile

### Saldo

Il saldo non deve essere inserito manualmente.

Può essere calcolato come:

- somma dei depositi
- meno somma dei prelievi

### Modifica dei movimenti

Per evitare incoerenze, è consigliato permettere con `PUT` solo la modifica di campi come:

- `description`

### Eliminazione dei movimenti

Potete definire una regola, ad esempio:

- è possibile eliminare solo l'ultimo movimento
- oppure è possibile eliminare un movimento solo se il saldo finale rimane valido

---

## Endpoint di esempio

### Elenco movimenti

`GET /accounts/1/transactions`

### Dettaglio movimento

`GET /accounts/1/transactions/5`

### Saldo attuale

`GET /accounts/1/balance`

### Conversione del saldo in un'altra valuta

`GET /accounts/1/balance/convert?to=USD`

### Nuovo deposito

`POST /accounts/1/deposits`

Body JSON:

```json
{
  "amount": 150.00,
  "description": "Versamento iniziale"
}
```

### Nuovo prelievo

`POST /accounts/1/withdrawals`

Body JSON:

```json
{
  "amount": 20.00,
  "description": "Prelievo bancomat"
}
```

### Modifica descrizione movimento

`PUT /accounts/1/transactions/5`

Body JSON:

```json
{
  "description": "Prelievo ATM aggiornato"
}
```

### Eliminazione movimento

`DELETE /accounts/1/transactions/5`

---

## Esempi di risposte JSON

### Saldo

```json
{
  "account_id": 1,
  "currency": "EUR",
  "balance": 130.00
}
```

### Saldo convertito

```json
{
  "account_id": 1,
  "from_currency": "EUR",
  "to_currency": "USD",
  "original_balance": 130.00,
  "converted_balance": 141.22,
  "rate": 1.0863
}
```

---

## API esterna da consumare

Per la conversione del saldo dovete usare **Frankfurter**, una API pubblica gratuita per tassi di cambio.

### Endpoint utili di Frankfurter

#### Elenco valute supportate

`GET https://api.frankfurter.dev/v1/currencies`

Restituisce un oggetto JSON con i codici valuta e il nome completo.

#### Tassi più recenti

`GET https://api.frankfurter.dev/v1/latest`

Per default usa `EUR` come valuta base.

#### Cambio della valuta base

`GET https://api.frankfurter.dev/v1/latest?base=USD`

Il parametro `base` permette di scegliere la valuta di partenza.

#### Limitare la risposta a valute specifiche

`GET https://api.frankfurter.dev/v1/latest?base=EUR&symbols=USD,GBP`

Il parametro `symbols` permette di richiedere solo alcune valute target.

### Come usarla nel progetto

Se il conto è in `EUR` e volete convertire il saldo in `USD`:

1. calcolate il saldo locale dal database
2. chiamate l'endpoint:

`GET https://api.frankfurter.dev/v1/latest?base=EUR&symbols=USD`

3. prendete il tasso `USD`
4. moltiplicate il saldo locale per quel tasso
5. restituite una risposta JSON progettata da voi

---

## Esempio di implementazione in Slim

Di seguito un esempio semplice di endpoint Slim che:

- calcola il saldo di un conto nel database
- legge il parametro `to`
- chiama Frankfurter
- restituisce il saldo convertito

```php
<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/accounts/{id}/balance/convert', function (Request $request, Response $response, array $args) use ($pdo) {
    $accountId = (int)$args['id'];
    $params = $request->getQueryParams();
    $to = strtoupper($params['to'] ?? '');

    if (!$to) {
        $response->getBody()->write(json_encode([
            'error' => 'Missing target currency'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    // Recupero conto
    $stmt = $pdo->prepare('SELECT id, currency FROM accounts WHERE id = ?');
    $stmt->execute([$accountId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        $response->getBody()->write(json_encode([
            'error' => 'Account not found'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(404);
    }

    $from = strtoupper($account['currency']);

    // Calcolo saldo: depositi - prelievi
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END), 0) -
            COALESCE(SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END), 0) AS balance
        FROM transactions
        WHERE account_id = ?
    ");
    $stmt->execute([$accountId]);
    $balance = (float)$stmt->fetchColumn();

    // Chiamata API esterna
    $url = "https://api.frankfurter.dev/v1/latest?base={$from}&symbols={$to}";
    $json = @file_get_contents($url);

    if ($json === false) {
        $response->getBody()->write(json_encode([
            'error' => 'External exchange API unavailable'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(502);
    }

    $data = json_decode($json, true);

    if (!isset($data['rates'][$to])) {
        $response->getBody()->write(json_encode([
            'error' => 'Target currency not supported'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }

    $rate = (float)$data['rates'][$to];
    $converted = round($balance * $rate, 2);

    $response->getBody()->write(json_encode([
        'account_id' => $accountId,
        'from_currency' => $from,
        'to_currency' => $to,
        'original_balance' => $balance,
        'converted_balance' => $converted,
        'rate' => $rate,
        'date' => $data['date'] ?? null
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});
```

---

## Errori da gestire

### `400 Bad Request`

- importo mancante
- importo non valido
- valuta target mancante
- valuta non supportata

### `404 Not Found`

- conto non trovato
- movimento non trovato

### `422 Unprocessable Entity` oppure `400 Bad Request`

- prelievo superiore al saldo disponibile

### `502 Bad Gateway`

- errore nella chiamata alla API esterna

---

## Cosa consegnare

Ogni gruppo deve consegnare:

1. codice del progetto
2. schema del database
3. elenco degli endpoint realizzati
4. almeno un esempio di chiamata per ogni endpoint
5. breve spiegazione delle scelte progettuali

---

## Criteri di valutazione

- correttezza degli endpoint REST
- qualità del modello dati
- correttezza della logica di business
- uso corretto del database
- integrazione con API esterna
- gestione degli errori
- chiarezza del JSON restituito

---

## Suggerimento finale

È preferibile realizzare **pochi endpoint ma ben fatti**, piuttosto che molti endpoint incompleti.
