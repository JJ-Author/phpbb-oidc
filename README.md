## Features of the fork
* users do not need to login anymore to view posts (oidc request is only requested on pages requiring credentials)
* initial support for phpbb docker setup (phpbb running in docker and behind HTTP (reverse) proxy)
* added some new config options (e.g. debug mode)

# phpbb-oidc
phpBB OpenID Connect plugin, based on [jumbojett/OpenID-Connect-PHP](https://github.com/jumbojett/OpenID-Connect-PHP).

## OpenID Connect
Per the [OpenID Connect specifications](https://openid.net/specs/openid-connect-core-1_0.html), phpbb-oidc should use `sub` as the sole identifier for the Service Provider (see [#5.1](https://openid.net/specs/openid-connect-core-1_0.html#IDToken)). However because PHPBB uses usernames as user identifiers, this plugin currently uses the preferredUsername attribute to identify users.


## Installation
* Unzip the folder and place it into `ext/ojathelonius/oidc`.
* Configure `config/oidc.yml` :
```yaml
# URL of the identity provider
# The following URI should show the OpenID discovery endpoint : http://idp/.well-known/openid-configuration
url: 'http://idp/'
# ID of the client in the IdP
clientId: 'clientId'
# Secret, if the client is set to private
secret: 'secret'
# Set to true if the client handles SSL/TLS on its own
ssl: true
# Set to true to create users automatically if they do not exist in PHPBB's database
createIfMissing: true
# if set to true: stableRedirect value provided will be used always as oidc redirect after successful login
# NOTE: this decreases usability (e.g. when going to reply to a post the user will end up on your redirect url (which should typically be the main page or a fixed post) instead the reply form) BUT it allows to run a phbb in a docker behind a proxy 
# If set to false: the OIDC library will try to detect the requested phpbb url autmatically and use it as redirect, which might fail in a docker and proxy environment.
stableRedirect: true
# only important if stable redirect is true. this should be the url point to the phpbb instance
redirect: 'https://databus.dbpedia.org/forum'
# if true on phpbb logout the OIDC session will also be logged out. If false logout will only apply for the phpbb session.
LogoutAllOidcOnForumLogout: false
# if set to true debug output of the plugin will be written straight into the html.
debug: false
```
* disable the re-authenticaton password prompt for ACP in case you would still like to access ACP after switching to OIDC authentication
* enable the authentication plugin via ACP
* create an OIDC user for the builtin (admin) account(s) in order to make sure nobody will register this user(s)

## Extend
phpbb-oidc uses standard OpenID user attributes. There are several additional attributes that could be retrieved the same way, such as groups, roles, user info...

In order to do this, the `OIDCUser` should be modified, and the field mapping must be done in both `createDefaultUserRow` and `updateUser`.

## Examples
### Example with Keycloak

```yaml
url: 'https://keycloak/auth/realms/my-realm/'
clientId: 'clientId'
secret: 'secret'
ssl: false
createIfMissing: true
```

## Caveats
* This plugin currently uses autologin for authentication : users are redirected to the IdP as soon as the login button is clicked or the action requires the user to login. therefore the password reauthenticate prompt for ACP needs to be removed in the phpbb source code in order to use ACP.
* OpenID-Connect-PHP does not implement Single Sign-Off yet : users will not be logged out automatically from phpBB when logging out from another SP
* Configuration still needs to be done through oidc.yml rather than through the ACP
* Still working on finding a way to show user friendly errors in the autologin auth flow, since `trigger_error` and `Exception` are not caught, unlike with the login flow

