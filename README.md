# phpbb-oidc
phpBB OpenID Connect plugin

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
ssl: false
# Set to true to create users automatically if they do not exist in PHPBB's database
createIfMissing: true
```

## Examples
### Example with Keycloak

```yaml
url: 'https://keycloak/auth/realms/my-realm/'
clientId: 'clientId'
secret: 'secret'
ssl: false
createIfMissing: true
```

## TODO
* Implement OpenID without autologin
* Allow configuration directly into phpBB ACP
* Implement log out feature
* Implement Single-Sign-Off, once [OpenID-Connect-PHP](https://github.com/jumbojett/OpenID-Connect-PHP/issues/116) supports it

