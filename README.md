### 项目 API 文档
- **yourServer**/myRpc/index.php?c=**yourControl**

### RPC 调用示例
- **yourServer**/client.php

### RPC 调试工具
- 用法：在控制台执行 php debug.php uri method "args, args"
- 示例：php yar_debug.php http://localhost/github/myRpc/myRpc/index.php?c=Test testSuccess "1"
- 返回结果很详细，非常利于调试

### 说明
- 之前在鸟哥的博客中看到 yar 的使用示例，感觉 yar 确实简单方便；然后考虑到防耦合，所以决定将 yar 嵌入到 controller+model 的结构中
- 这里采用了单一入口，控制器的调用通过传参 c 决定，再由 index.php 判断并实例化；
- c 参数错误会返回响应码601/602；
