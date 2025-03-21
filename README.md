# Preishoheit Plugin für Shopware 6.6.x

## 🇩🇪 Deutsche Version
- [English version below](#gb-english-version)

Mit diesem Plugin kannst du dynamisch die Preise deiner Shopware-Produkte mithilfe der Preishoheit API verwalten und optimieren.

---

## 🚀 Funktionen und Vorteile

- **Automatische Preisanpassung** deiner Produkte auf Basis aktueller Marktpreise.
- **Einfache Verwaltung** über die Shopware-Administration.
- **Flexible Konfiguration** über das Shopware-Backend (API-Key, Intervalle, Felder).
- **Persistentes Produkt-Mapping** für eine einfache Wiederverwendung.

---

## ⚙️ Installation

1. **Plugin hochladen:**
   Lade die ZIP-Datei im Shopware-Adminbereich unter  
   `Einstellungen → System → Plugins` hoch.

2. **Plugin aktivieren:**
   Nach dem Hochladen das Plugin installieren und aktivieren.

3. **Plugin konfigurieren:**
   Trage den von Preishoheit bereitgestellten API-Key und API-Endpoint unter  
   `Einstellungen → System → Plugins → Preishoheit → Konfiguration` ein.

4. **Cronjob einrichten (optional, empfohlen):**
   Der Cronjob läuft standardmäßig alle 5 Minuten und prüft den Status deiner Preisanfragen.

---

## 🖥️ Nutzung im Shopware Admin

Nach erfolgreicher Installation findest du im Admin folgende Tabs:

- **General**: Übersicht zu Jobs, Status und letzten API-Checks.
- **Jobs**: Jobs zur dynamischen Preisanpassung erstellen und verwalten.
- **Results**: Ergebnisse verwalten und manuell anpassen (Titel, Preis, EAN).
- **Mapping**: Externe Produkt-IDs mit Shopware-Produkten verknüpfen.
- **Settings**: Cronjob-Intervalle und Felder zur automatischen Datenüberschreibung konfigurieren.

---

## 🛠️ Technische Details

### Voraussetzungen:
- Shopware-Version: 6.6.x oder höher
- PHP 8.2+
- Composer
- Aktiver Preishoheit API-Key

### Composer-Abhängigkeiten:
- Guzzle HTTP-Client

### Shopware DAL Integration:
- Vollständig integriert mit Shopware DAL & Entity-System.

---

## 📝 Lizenz
Dieses Plugin ist proprietär.  
© 2024 BOW E-Commerce Agentur GmbH

---

## 🧑‍💻 Support
Bei Fragen oder Problemen wende dich an: [info@bow-agentur.de](mailto:info@bow-agentur.de)

---

## 🇬🇧 English Version
- [Deutsche Version oben](#deutsche-version)

With this plugin, you can dynamically manage and optimize the prices of your Shopware products using the Preishoheit API.

---

## 🚀 Features & Benefits

- **Automatic price adjustments** for your products based on current market prices.
- **Easy administration** via Shopware administration interface.
- **Flexible configuration** via Shopware backend (API key, intervals, fields).
- **Persistent product mapping** for easy reuse.

---

## ⚙️ Installation

1. **Upload the plugin:**
   Upload the ZIP file in your Shopware admin panel under  
   `Settings → System → Plugins`.

2. **Activate the plugin:**
   After upload, install and activate the plugin.

3. **Configure the plugin:**
   Enter the API key and API endpoint provided by Preishoheit under  
   `Settings → System → Plugins → Preishoheit → Configuration`.

4. **Set up Cronjob (optional, recommended):**
   The cronjob runs every 5 minutes by default, checking your pricing job statuses.

---

## 🖥️ Usage in Shopware Admin

After installation, you'll find these tabs in your Shopware Admin:

- **General**: Overview of jobs, status, and recent API checks.
- **Jobs**: Create and manage jobs for dynamic pricing adjustments.
- **Results**: Manage and manually adjust results (title, price, EAN).
- **Mapping**: Link external product IDs with Shopware products.
- **Settings**: Configure cronjob intervals and automatic data overwrite fields.

---

## 🛠️ Technical details

### Requirements:
- Shopware version: 6.6.x or higher
- PHP 8.2+
- Composer
- Active Preishoheit API key

### Composer dependencies:
- Guzzle HTTP client

### Shopware DAL integration:
- Fully integrated with Shopware DAL & Entity System.

---

## 📝 License
This plugin is proprietary.  
© 2024 BOW E-Commerce Agentur GmbH

---

## 🧑‍💻 Support
For questions or issues, contact: [info@bow-agentur.de](mailto:info@bow-agentur.de)
