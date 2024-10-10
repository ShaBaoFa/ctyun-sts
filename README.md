# [ctyun-sts](https://github.com/wlfpanda1012/ctyun-sts)

```
composer wlfpanda1012/ctyun-sts -oW
```


- ~~注意： 当前 天翼云 **OOS** 暂时无法使用 `policy` 去细分权限。 24年9月底左右和天翼云的技术人员进行了对接。当前 `policy` 确实没有生效。 目前是直接给予完成的 角色 （`RAM`） 权限。~~
- 解决方案: 2024年10月10日,接到 天翼云相关工作人员的电话,在使用 `StsToken` 去获取资源时，如果希望 `Policy` 生效，需要将 **`OOS`** 的 `endpoint` 设置为 `oos-hazz.ctyunapi.cn` 目前只有郑州区域的 `OOS` 服务支持解析 `Policy`，其他地区皆不支持。。
## OOS
OOS 为用户提供临时授权访问。此操作用来获取临时访问密钥。子用户默认拥有调用此接口
的权限。如果配置了禁止子用户调用该接口的 IAM 策略，该策略不会生效。
**STS（Security Token Service）** 是为云计算用户提供临时访问令牌的 `Web` 服务。通过 `STS`，可
以为第三方应用或用户颁发一个自定义时效的访问凭证。第三方应用或用户可以使用该访问
凭证直接调用 `OOS API`，或者使用 `OOS` 提供的 `SDK` 来访问 `OOS API`。
使用临时授权访问 `OOS API` 时，用户需要将安全令牌（**SessionToken**）携带在请求 `header` 中
或者预签名 `URL` 中。携带在请求 `header` 中，`V4` 和 `V2` 签名的 `X-Amz-Security-Token` 请求头
不区分大小写。携带在预签名 `URL` 中，`V4` 签名的标头为“`X-Amz-Security-Token`”，`V2` 签名
的标头为“`x-amz-security-token`”。
注意：使用临时访问令牌（`AccessKeyId`、`SecretAccessKey`、`SessionToken`）的用户不能调用
该接口。使用临时访问令牌调用其他接口时，权限同生成该临时访问令牌的用户。为了安全
起见，不建议根用户调用该接口。
