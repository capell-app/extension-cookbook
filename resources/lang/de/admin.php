<?php

declare(strict_types=1);

return [
    'navigation' => [
        'extension_cookbook' => 'Capell Extension Cookbook',
        'entries' => 'Referenzeinträge',
    ],
    'section' => [
        'label' => 'Hinweis zum Extension Cookbook',
        'description' => 'Ein kurzer Referenzhinweis, der zeigt, wie ein Paket einen Inhaltsabschnitt bereitstellt.',
        'note' => 'Hinweis',
    ],
    'settings' => [
        'title' => 'Capell Extension Cookbook',
        'enabled' => 'Extension Cookbook aktivieren',
    ],
    'stats' => [
        'enabled' => 'Aktivierte Beispiele',
    ],
    'dashboard' => [
        'title' => 'Erweiterungsabdeckung',
        'description' => 'Prüfen Sie die Abdeckung der Beiträge, die dieses Extension Cookbook bereitstellt.',
        'registrar_title' => 'Abdeckung der Registrar-Methoden',
        'registrar_description' => 'Sichere Methoden werden demonstriert; gefährliche globale Ersetzungsstellen bleiben bewusst inaktiv.',
    ],
    'schema' => [
        'help' => 'Die Felder der Referenzeinträge gehören dem Paket; dieser Hinweis dient nur der Darstellung.',
    ],
    'configurator' => [
        'entry' => 'Schlüssel des Referenzeintrags',
    ],
    'fields' => [
        'title' => 'Titel',
        'slug' => 'Schlüssel',
        'enabled' => 'Aktiviert',
    ],
    'widget' => [
        'label' => 'Capell Extension Cookbook Widget',
        'title' => 'Titel',
        'summary' => 'Zusammenfassung',
    ],
    'actions' => [
        'open' => 'Capell Extension Cookbook öffnen',
    ],
    'workflow' => [
        'label' => 'Capell Extension Cookbook benötigt ein aktiviertes Beispiel',
        'owner' => 'Capell Extension Cookbook',
        'action' => 'Extension Cookbook öffnen',
    ],
    'coverage' => [
        'groups' => [
            'core' => 'Core-Beiträge',
            'admin' => 'Admin-Beiträge',
            'frontend' => 'Frontend-Beiträge',
        ],
        'statuses' => [
            'demonstrated' => 'Demonstriert',
            'inactive' => 'Bewusst inaktiv',
        ],
        'registrar' => [
            'groups' => [
                'safe' => 'Demonstrierte sichere Registrar-Methoden',
                'inactive' => 'Gefährliche globale Ersetzungsstellen (bewusst inaktiv)',
            ],
            'methods' => [
                'page' => 'Registriert eine paketeigene Filament-Seite.',
                'resource' => 'Registriert eine paketeigene Filament-Ressource in einer eigenen Gruppe.',
                'extension_dashboard_widget' => 'Fügt dem Erweiterungs-Dashboard ein begrenztes Widget hinzu.',
                'filament_dashboard_widget' => 'Fügt dem Haupt-Dashboard eine begrenzte Übersichtsstatistik hinzu.',
                'resource_header_action_extender' => 'Fügt nur der Listenseite der Referenzeinträge eine Aktion hinzu.',
                'schema_extender' => 'Fügt der Seitenstruktur einen paketeigenen Hinweis hinzu.',
                'configurator' => 'Registriert einen paketeigenen Blueprint-Konfigurator.',
                'settings_schema' => 'Registriert das Einstellungsschema des Pakets.',
                'extension_page' => 'Veröffentlicht die Paketseite über die Registry für Erweiterungsseiten.',
                'dashboard_page' => 'Würde das Host-Dashboard global ersetzen.',
                'extension_removal_coordinator' => 'Würde den Koordinator für das Entfernen von Erweiterungen des Hosts global ersetzen.',
                'pending_theme_install_provider' => 'Würde die Theme-Installationspipeline des Hosts global verändern.',
            ],
        ],
        'types' => [
            'admin-page' => 'Stellt eine paketeigene Admin-Seite bereit.',
            'admin-resource' => 'Verwaltet paketeigene Datensätze im Admin-Bereich.',
            'admin-action-extender' => 'Erweitert eine bestehende Admin-Ressourcenseite um eine begrenzte Aktion.',
            'section' => 'Stellt einen paketeigenen Inhaltsabschnitt bereit.',
            'page-type' => 'Registriert einen paketeigenen Seitentyp.',
            'dashboard-widget' => 'Fügt einem Admin-Dashboard ein paketeigenes Widget hinzu.',
            'builder-block' => 'Fügt dem Seiten-Builder einen paketeigenen Block hinzu.',
            'overview-stat' => 'Fügt einem Admin-Dashboard eine paketeigene zusammenfassende Statistik hinzu.',
            'schema-extender' => 'Erweitert eine unterstützte Admin-Schema-Schnittstelle.',
            'configurator' => 'Fügt einer unterstützten Admin-Oberfläche einen paketeigenen Konfigurator hinzu.',
            'model' => 'Registriert ein paketeigenes Modell für die Capell-Erkennung.',
            'permission' => 'Deklariert Berechtigungen für paketeigenes Admin-Verhalten.',
            'route' => 'Registriert eine paketeigene Route.',
            'setting' => 'Registriert paketeigene Einstellungen.',
            'page-variation' => 'Registriert eine paketeigene Seitenvariante.',
            'frontend-component' => 'Registriert eine paketeigene Frontend-Komponente.',
            'content-widget' => 'Registriert ein paketeigenes Inhalts-Widget.',
            'render-hook' => 'Trägt zu einem unterstützten Frontend-Render-Hook bei.',
            'asset' => 'Veröffentlicht paketeigene Frontend-Assets.',
            'migration' => 'Deklariert paketeigene Datenbankmigrationen.',
            'scheduled-job' => 'Registriert paketeigene geplante Aufgaben.',
            'console-command' => 'Registriert einen paketeigenen Konsolenbefehl.',
            'agent-capability' => 'Deklariert eine paketeigene Agent-Fähigkeit.',
            'content-graph' => 'Trägt eine paketeigene Extraktion für den Inhaltsgraphen bei.',
            'health-check' => 'Registriert eine paketeigene Integritätsprüfung.',
            'workflow-attention' => 'Trägt einen berechtigungsabhängigen Hinweis für den Workflow bei.',
            'outbound-event' => 'Deklariert ein paketeigenes ausgehendes Ereignis.',
            'blueprint-subject' => 'Registriert ein paketeigenes Blueprint-Subjekt.',
        ],
    ],
];
