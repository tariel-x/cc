## Register

```json
{
	"jsonrpc": "2.0", 
	"method": "register", 
	"params": {
		"scheme": [
			{
				"in": true,
        "priority": 0,
				"scheme": ["bbb"],
				"type": "none"
			}
		],
		"service": {
			"name": "test",
			"address": []
		}
	}, 
	"id": 1
}
```

## Get all

```json
{
	"jsonrpc": "2.0", 
	"method": "getAll", 
	"params": {}, 
	"id": 1
}
```

## Get

```json
{
  "jsonrpc": "2.0",
  "method": "get",
  "params": {
    "schemes": [
      {
        "type": "none",
        "in": true,
        "scheme": [
          "bbb"
        ]
      }
    ]
  },
  "id": 1
}
```