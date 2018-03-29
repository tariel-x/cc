## Register contract

```json
{
	"jsonrpc": "2.0", 
	"method": "registerContract", 
	"params": {
		"schemes": [
			{
				"in": true,
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

## Remove contract

```json
{
	"jsonrpc": "2.0", 
	"method": "removeContract", 
	"params": {
		"schemes": [
			{
				"in": true,
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

## Register usage

```json
{
	"jsonrpc": "2.0", 
	"method": "registerUsage", 
	"params": {
		"schemes": [
			{
				"in": true,
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

## Remove usage

```json
{
	"jsonrpc": "2.0", 
	"method": "removeUsage", 
	"params": {
		"schemes": [
			{
				"in": true,
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