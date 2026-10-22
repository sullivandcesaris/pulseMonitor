# Monitoring Network Security

PulseMonitor applies outbound restrictions in layers:

- Monitor URLs accept only HTTP and HTTPS.
- URLs containing credentials are rejected.
- Only ports configured in `MONITOR_ALLOWED_PORTS` are accepted; the default is `80,443`.
- Hostnames are resolved before a request and private, loopback, link-local, reserved, and metadata addresses are rejected.
- Redirects are disabled so a public URL cannot redirect the worker into a restricted network.
- Connection and request timeouts limit how long a worker can hold an outbound request.

The Docker bridge network still permits outbound traffic because public monitoring targets require internet access. Host or cloud firewall policy should provide the final egress control in production: allow DNS and HTTP/HTTPS egress only, block RFC1918/link-local/metadata ranges, and apply an outbound proxy or network policy where available. Application validation is defense in depth and does not replace infrastructure-level egress controls.
