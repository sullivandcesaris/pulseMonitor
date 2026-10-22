# Security Controls

- Authentication and verified-email middleware protect the dashboard, monitor,
  incident, and settings routes.
- Monitor policies and organization-scoped queries prevent cross-organization
  access.
- Monitor targets accept only HTTP/HTTPS, approved ports, public DNS/IP
  destinations, and no credentials. Redirects are disabled.
- Monitor management routes are rate-limited to 60 requests per minute per user.
- No monitor credentials are stored; the current MVP supports public targets only.
- Structured logs include correlation and operational metadata but do not include
  request bodies, authorization headers, passwords, or mail credentials.
- Sentry default PII collection is disabled.

Security review tests cover authentication, authorization, organization isolation,
SSRF/private-address rejection, redirect behavior, and rate-limit configuration.
