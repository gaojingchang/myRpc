### 项目 API 文档
- **yourdomain**/myRpc/index.php?c=**yourControl**

### RPC 调用示例
- **yourdomain**/client.php

### 说明
- 之前在鸟哥的博客中看到 yar 的使用示例，感觉 yar 确实简单方便；然后考虑到防耦合，所以决定将 yar 嵌入到 controller+model 的结构中
- 这里采用了单一入口，控制器的调用通过传参 c 决定，再由 index.php 判断并实例化；
- c 参数错误会返回404错误
- 另外我调用时发现 warning 之类的低级别错误不会返回；所以急需寻找一个便于调试 RPC 的方法(记录日志?)；