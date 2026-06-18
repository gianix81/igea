# Igea Club — redesign dashboard (tema acqua + chiaro/scuro)

File drop-in per il tuo progetto PHP. Copia mantenendo i percorsi:

```
igea-redesign/public/assets/css/app.css   ->  igea/public/assets/css/app.css
igea-redesign/views/layout.php            ->  igea/views/layout.php
igea-redesign/views/dashboard/index.php   ->  igea/views/dashboard/index.php
```

Fai prima un backup dei 3 file originali (es. `app.css.bak`).

## Cosa cambia
- **Tema "acqua"** con palette teal/aqua applicata a tutta l'app tramite variabili CSS.
- **Interruttore chiaro/scuro** nella barra in alto (sole / luna). La scelta è salvata in `localStorage` (`igea-theme`) e applicata prima del paint, senza flash. Default: scuro.
- **Dashboard** ridisegnata: banda KPI, "Ultimi movimenti" con chip per reparto (Bar/Ristorante/Cassa/Reception) e badge stato, riquadro incasso vs consumazioni, scorciatoia alla Cassa.
- **Responsive**: la griglia KPI passa a 3 e poi 2 colonne, il contenuto si impila, la nav diventa scorrevole su mobile.

## Note
- Nessun cambiamento ai dati: usa gli stessi `$stats` e `$latest` che `public/index.php` già passa alla vista.
- Bootstrap resta caricato (tabelle, bottoni, form delle altre viste continuano a funzionare). Le classi esistenti (`.metric`, `.place`, `.product-button`, `.balance`, `.login-box`) sono preservate e ritematizzate, quindi anche Bar, Posti, Cassa e Login seguono il nuovo tema.
- `color-mix()` (usato per i badge stato) richiede browser recenti; se ti serve compatibilità più ampia te lo sostituisco con colori fissi.

Per applicare lo stesso tema/intestazione anche alle altre viste (Bar, Cassa, Prenotazioni, Posti) posso prepararti i rispettivi file.
