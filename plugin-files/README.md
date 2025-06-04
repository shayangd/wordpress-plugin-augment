# wordpress-plugin-augment
wordpress plugin using augment code

1. Figma API key (personal access token), Account Settings> Security Tab> Generate a Personal Access Token. (You can only copy once)
2. Install node in mac to run mcp server
```
brew install node
```
3. 
    1. Goto Augment Settings in VS Code and Add Figma MCP Server
    2. Click on Import from JSON
    3. Copy JSON from https://mcp.so/server/Figma-Context-MCP/GLips
    4. Replace FIGMA_API_KEY, Click Add.
```
Name: figma-developer-mcp
Command: npx -y figma-developer-mcp --stdio
ENVIRONMENT VARIABLE: FIGMA_API_KEY: figd_ZSrWXhkhDFWQKgQmyGkGK8oar01S7_xxxxxxxx
```
⚠️ not working on mac, do below
```
export PATH="/opt/homebrew/bin:$PATH"
sudo ln -s /opt/homebrew/bin/node /usr/local/bin/node
```
Specify the full path when running the command, update mcp server json
```
PATH="/opt/homebrew/bin:$PATH" npx -y figma-developer-mcp --stdio
```

4. Go to VS Code, Open Augment Side Panel, Change From Chat to Agent (otherwise Figma MCP server wont work)
5. Prompts
```
1. can you read this figma
https://www.figma.com/design/0ldaPxf1GO3APTPP1qrCMn/Wellows---AAAI-Design?node-id=3016-29452&p=f&t=6xtRTvne0knLptDZ-0
2. now access this figma and create a wordpress plugin in this repo with the whole functionality. backend frontend and make sure the design of frontend has same layout as in figma
3. create an empty wordpress site and use this plugin
```



