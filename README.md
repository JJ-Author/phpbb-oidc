# phpbb-oidc
phpBB OpenID Connect plugin

## OpenID Connect
phpbb-oidc follows the [OpenID Connect specifications](https://openid.net/specs/openid-connect-core-1_0.html), and as such, uses `sub` as the sole identifier for the Service Provider (see [#5.1](https://openid.net/specs/openid-connect-core-1_0.html#IDToken)).

## Examples
### Example with Keycloak

```yaml
url: 'https://keycloak/auth/realms/my-realm/'
clientId: 'clientId'
secret: 'secret'
ssl: false
```

