# Aria Dolce - Setup con Formspree

Questo sito usa Formspree per gestire gli invii email dal form. Non serve server PHP, Composer, o configurazione SMTP.

## 1) Registrazione su Formspree

1. Vai su: https://formspree.io/
2. Accedi o crea un account (gratuito)
3. Crea un nuovo form
4. Sostituisci `YOUR_FORM_ID` nel file `index.html` con il tuo Form ID

### Dove modificare:

Apri [index.html](index.html) e cerca:

```javascript
const response = await fetch('https://formspree.io/f/YOUR_FORM_ID', {
```

Sostituisci `YOUR_FORM_ID` con il tuo ID di Formspree (es: `xyzabc123`).

## 2) Come funziona

- l'utente riempie il form
- i dati vanno a Formspree
- Formspree invia a ariadolce13@gmail.com e ariadolce@pec.it
- nessun backend richiesto

## 3) Avvia il sito

Puoi aprire il sito da qualsiasi posto:
- file locale: `file:///path/to/index.html`
- server PHP: `http://localhost:8000`
- hosting: il tuo dominio

Formspree gestisce tutto il resto.

## 4) Limiti Formspree

- Gratis: 50 submit al mese
- A pagamento: submit illimitati
- Niente pubblicità, niente vendita di dati

Perfetto per un sito di pasticceria.

