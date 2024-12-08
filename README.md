# Qwentes Backend Application

Il tempo massimo per la consegna è di 7 giorni dalla ricezione delle specifiche via email.

Lo sviluppo può essere fatto su GitHub, GitLab o qualsiasi altro portale. Una volta terminato inviare il link della repo
via email (la stessa email che ha inviato queste specifiche).

Chiediamo il candidato di eseguire l'esercizio in PHP.

## Premessa

L'esercizio consiste nello sviluppare le API REST di un'ipotetica piattaforma di content publishing.

La piattaforma permette la gestione degli utenti, la gestione dei post e la gestione dei commenti.

Il file [swagger.yml](swagger.yml) contiene la documentazione delle API.

E' consentito solo l'utilizzo del DB MySQL.

E' preferibile l'utilizzo di microframework come Slim

Nel caso in cui non si riuscisse a terminare tutto l'esercizio la cosa che verrà presa piu in considerazione è la
qualità del codice, non la quantità:

- organizzazione del codice 
- corretta divisione tra dominio e componenti infrastrutturali

---

## Requisiti

Al minimo di tutto l'esercizio deve includere i punti da 1 a 4 elencati di seguito. **Tutti i punti successivi al 4 sono a
discrezione del candidato**

### 1) Docker setup

Includere una configurazione di `Docker` o `Docker Compose` e le istruzioni per lanciare l'applicativo ed eseguire i
comandi da cli. L'esercizio verrà lanciato su una macchina vergine dove è presente solo Docker quindi è
molto importante che la configurazione di Docker includa tutte le dipendenze necessarie (Webserver, DB, ecc)

### 2) CRUD User

Implementare API per la gestione degli utenti come documentato sullo swagger.
Implementare anche migrazioni + fixtures necessarie al setup del DB.

I requisiti impongono che:

- Non sono ammessi più utenti con la stessa email
- La password dell'utente deve rispettare questi vincoli:
    - lunghezza minima 6 caratteri
    - deve contenere almeno 1 numero
    - deve contenere almeno 1 carattere minuscolo
    - deve contenere almeno 1 carattere maiuscolo
    - deve contenere almeno 2 caratteri speciali tra questi: `,`, `.`, `:`, `;`, `-`, `_`, `$`, `%`, `&`, `(`, `)`, `=`
    - non può contenere 2 caratteri identici consecutivi
    - non può contenere la local-part dell'indirizzo email (la local-part è la stringa prima del carattere `@`)

### 3) Comando da CLI

Realizzare un comando da CLI per la creazione degli utenti che rispetti questa signature:

```
> user:create <givenName> <familyName> <email> <password>
```

### 4) Autenticazione

Il processo di autenticazione avviene invocando la rotta `POST /login` (l'unica non protetta da autenticazione) che
verifica la bontà delle credenziali fornite e ritorna un JWT che contiene queste informazioni:

Header:

```
{
  "alg": "HS256",
  "typ": "JWT"
}
```

Payload:

```
{
  "name": "[nome e cognome dell'utente loggato]",
  "email": "[email dell'utente loggato]",
  "given_name": "[nome dell'utente loggato]",
  "family_name": "[cognome dell'utente loggato]",
  "exp": 1516242622, // timestamp che indica la scadenza del token
  "iat": 1516239022 // timestamp con la creazione del token
}
```

Il JWT deve avere un lifetime di 60 minuti.

### 5) CRUD + ricerca Posts (nice to have)

Implementare la logica e le modifiche al DB necessarie per il CRUD + la ricerca dei posts.

La ricerca google-like con il parametro `q` deve cercare sia nel `title` che nel `body` del post.

### 6) CRUD Commenti (nice to have)

Implementare la logica e le modifiche al DB necessarie per il CRUD dei commenti.

---

## API specs

### HTTP request headers

Tutte le richieste devono includere almeno questi headers:

- `Content-Type: application/json`
- `Accept: application/json`
- `Authorization: Bearer <JWT ottenuto dopo l'autenticazione>` (questo deve essere incluso in tutte le richieste a parte
  quella per l'autenticazione)

In caso di assenza di uno o più di questi headers le API devono ritornare un errore.

### Query parameters

#### Sorting

il parametro `sort[]` viene usato da tutte le API che forniscono la possibilità di ordinare i risultati.

I valori che si possono passare a `sort` rispettano un formato preciso: `+/-<sortField>`. Dove il `+` indica che si
intende sortare `ASC` il `-` che si vuole sortare `DESC`. Se non è passata alcuna indicazione il comportamento di
default è `ASC`.

E' possibile indicare più campi sui quali si intende sortare usando più parametri `sort[]`. Es:

```
?sort[]=-createdAt&sort[]=+quantityOfLikes
```

In questo caso il client sta indicando la volontà di ordinare le risorse per `createdAt` DESC e `quantityOfLikes` ASC.

#### Paging

Il paging delle risorse avviene usando i parametri:

- `page` il numero di pagina che si intende richiedere (inizia da 1)
- `perPage` il numero di risorse per pagina ritornate (minimo 1)

#### Altro

Come per il `sorting` tutte le volte che c'è bisogno di passare più di un valore ad un parametro in query string si usa
il formato con `[]` alla fine del nome del parametro.

Come esempio si può usare l'API `GET /posts` che per filtrare i post per tags usa il parametro `tags[]`. Salvo diverse
indicazioni i valori passati vengono sempre usati in una combinazioni di tipo `OR`.

Esempio:

```
GET /posts?tags[]=js&tags[]=backend
```

In questo caso le API devono ritornare tutti i post taggati `js` o `backend`.

### Errori

Lo swagger delle API non fornisce volutamente indicazioni sulla gestione degli errori e dei response di errore.

E' fortemente consigliato l'utilizzo dello standard [RFC7807](https://datatracker.ietf.org/doc/html/rfc7807) ma il
candidato è libero di procedere con l'implementazione che preferisce l'importante è rispettare i business requirements
indicati di seguito.

---
