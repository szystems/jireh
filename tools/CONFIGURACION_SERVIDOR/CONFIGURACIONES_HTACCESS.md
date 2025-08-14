# ==========================================
# CONFIGURACIONES .HTACCESS PARA JIREH
# ==========================================
# Este archivo contiene diferentes configuraciones que puedes usar
# según el estado de tu servidor y dominio

# ==========================================
# OPCIÓN 1: DESARROLLO LOCAL (ACTUAL)
# ==========================================
# Usa esto mientras desarrollas localmente
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# ==========================================
# OPCIÓN 2: SOLO HTTPS (CUANDO SSL ESTÉ LISTO)
# ==========================================
# Descomenta cuando tengas SSL configurado
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     
#     # Forzar HTTPS (excepto localhost)
#     RewriteCond %{HTTPS} off
#     RewriteCond %{HTTP_HOST} !^localhost$ [NC]
#     RewriteCond %{HTTP_HOST} !^127\.0\.0\.1$ [NC]
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
#     
#     # Resto de configuración Laravel...
# </IfModule>

# ==========================================
# OPCIÓN 3: PRODUCCIÓN COMPLETA
# ==========================================
# Usa cuando todo esté configurado correctamente
# <IfModule mod_rewrite.c>
#     <IfModule mod_negotiation.c>
#         Options -MultiViews -Indexes
#     </IfModule>
# 
#     RewriteEngine On
# 
#     # Forzar HTTPS
#     RewriteCond %{HTTPS} off
#     RewriteCond %{HTTP_HOST} !^localhost$ [NC]
#     RewriteCond %{HTTP_HOST} !^127\.0\.0\.1$ [NC]
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# 
#     # Forzar dominio específico
#     RewriteCond %{HTTP_HOST} !^www\.software\.jirehautomotriz\.com$ [NC]
#     RewriteCond %{HTTP_HOST} !^localhost$ [NC]
#     RewriteCond %{HTTP_HOST} !^127\.0\.0\.1$ [NC]
#     RewriteRule ^(.*)$ https://www.software.jirehautomotriz.com/$1 [L,R=301]
# 
#     # Handle Authorization Header
#     RewriteCond %{HTTP:Authorization} .
#     RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
# 
#     # Redirect Trailing Slashes If Not A Folder...
#     RewriteCond %{REQUEST_FILENAME} !-d
#     RewriteCond %{REQUEST_URI} (.+)/$
#     RewriteRule ^ %1 [L,R=301]
# 
#     # Send Requests To Front Controller...
#     RewriteCond %{REQUEST_FILENAME} !-d
#     RewriteCond %{REQUEST_FILENAME} !-f
#     RewriteRule ^ index.php [L]
# </IfModule>

# ==========================================
# INSTRUCCIONES DE IMPLEMENTACIÓN
# ==========================================
# 1. Primero sube los archivos con la configuración actual (desarrollo)
# 2. Verifica que Laravel funcione correctamente
# 3. Configura el SSL en tu hosting
# 4. Activa la configuración HTTPS
# 5. Verifica que los DNS apunten correctamente
# 6. Activa la redirección de dominio
