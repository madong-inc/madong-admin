
/**
 * 网站核心配置接口 
 * @desc 包含站点基础信息、备案数据及安全认证配置 
 */
export interface SiteConfig {
    /** 
     * 站点开放状态 
     * @example "1" - 启用状态 
     * @example "0" - 维护状态 
     */
    site_open: '0' | '1';

    /** 站点访问地址 (需包含协议头) */
    site_url: string;

    /** 站点名称 (中文/英文品牌标识) */
    site_name: string;

    /** 
     * 站点LOGO地址 
     * @pattern 支持SVG/PNG格式资源路径 
     */
    site_logo: string;

    /** 
     * 公安备案编号 
     * @example "京公网安备 2024042441号-2"
     */
    site_network_security: string;

    /** 站点描述信息 (SEO优化关键字段) */
    site_description: string;

    /** ICP备案号 (省级备案编号) */
    site_record_no: string;

    cdn_url?:string;

    cdn_url_params?:string;

    /** 
     * ICP备案跳转链接 
     * @link https://beian.miit.gov.cn/ 
     */
    site_icp_url: string;

    /**
     * 公安备案跳转链接 
     * @desc 当前为空时需要隐藏备案跳转按钮 
     */
    site_network_security_url: string | null;
}



/**
* 文件传输策略配置 
* @desc 控制文件上传/下载的精细化策略 
*/
export interface FilePolicyConfig {
    /**
     * 存储模式 
     * @default "local"
     * @enum ["local", "remote", "hybrid"] - 混合模式需配合CDN配置 
     */
    mode: "local" | "remote" | "hybrid";

    /**
     * 单文件大小限制（单位：MB）
     * @minimum 1   - 允许最小1MB 
     * @maximum 2048 - 系统最大支持2GB 
     */
    single_limit: number;

    /**
     * 总传输容量限制（单位：MB）
     * @note 当模式为remote时自动提升至5000MB 
     */
    total_limit: number;

    /**
     * 并发传输数 
     * @minimum 1 
     * @maximum 50 - 受服务器性能限制 
     */
    nums: number;

    /**
     * 禁止传输的文件类型 
     * @pattern 以逗号分隔的扩展名，不包含点符号 
     * @example "php,ext,exe" - 高危文件类型过滤 
     */
    exclude: string;

    /**
     * 扩展校验规则（可选）
     * @beta 实验性功能 
     */
    advanced?: {
        /** 哈希校验算法 */
        hash_algorithm?: "MD5" | "SHA256";
        /** 病毒扫描开关 */
        virus_scan?: boolean;
    };
}


/**
* SMTP 邮件服务配置 
* @desc 包含加密协议、认证信息及发件人配置 
* @link [QQ邮箱SMTP文档](https://service.mail.qq.com/detail/0/75) 
*/
export interface EmailConfig {
    /**
     * 传输层安全协议 
     * @enum ["ssl", "tls"] - QQ邮箱强制要求SSL加密 
     * @example "ssl" - 标准加密协议 
     */
    SMTPSecure: "ssl" | "tls";

    /**
     * SMTP服务器地址 
     * @pattern 域名或IP地址 
     * @default "smtp.qq.com" 
     */
    Host: string;

    /**
     * 服务端口号 
     * @default 465 - SSL协议专用端口 
     * @see 587 - TLS协议备用端口 
     */
    Port: 465 | 587;

    /**
     * 认证邮箱账号 
     * @format 需开启SMTP服务的完整邮箱 
     * @example "kzhzjdyw888@qq.com" 
     */
    Username: `${string}@${string}.${string}`;

    /**
     * SMTP授权码 
     * @security Sensitive - 需加密存储 
     * @note 非QQ邮箱登录密码 
     */
    Password: string;

    /**
     * 发件人邮箱 
     * @rule 需与认证账号一致 
     * @example "service@company.com" 
     */
    From: `${string}@${string}.${string}`;

    /**
     * 发件人显示名称 
     * @maxLength 30 - 客户端显示限制 
     * @example "【企业名称】系统通知"
     */
    FromName: string;

    /**
     * 高级连接配置（可选）
     */
    advanced?: {
        /** 超时时间（毫秒） */
        timeout?: number;
        /** 调试模式日志级别 */
        debugLevel?: "none" | "basic" | "full";
    };
}

/**
 * 短信服务全局配置 
 * @desc 包含服务商认证、签名模板及服务开关 
 */
export interface SMSConfig {
    /**
     * 服务开关 
     * @enum ["0", "1"] - 0=禁用，1=启用 
     * @security 敏感操作需二次验证 
     */
    enable: "0" | "1";

    /**
     * 服务商访问密钥ID 
     * @pattern 由服务商颁发的18-32位数字ID 
     * @example "234813346262818816"
     */
    access_key_id: string;

    /**
     * 服务商访问密钥 
     * @security Sensitive - 需加密存储 
     * @note 泄漏将导致账号被盗风险 
     */
    access_key_secret: string;

    /**
     * 短信签名模板 
     * @template 必须包含 {code} 占位符 
     * @example "【企业名称】，你的验证码是{code}，有效期5分钟。"
     */
    sign_name: string;

    /**
     * 高级配置（可选）
     */
    advanced?: {
        /** 请求超时时间（毫秒） */
        timeout?: number;
        /** 失败重试次数 */
        retry_times?: number;
        /** 地域节点 */
        region_id?: "cn-hangzhou" | "ap-southeast-1";
    };
}

/**
* 本地文件上传配置 
* @desc 包含存储路径、域名配置等核心参数 
*/
export interface LocalUploadConfig {
    /**
     * 文件存储根目录 
     * @example "public" - 对应项目根目录下的/public文件夹 
     * @pattern 仅允许字母、数字、下划线组合 
     */
    root: string;

    /**
     * 动态子目录名称 
     * @example "upload" - 最终路径为 /public/upload/
     * @rule 禁止包含路径分隔符(/或\)
     */
    dirname: string;

    /**
     * 资源访问域名 
     * @format 需包含协议头和端口号（非80/443端口时必须显式声明）
     * @example "http://cdn.example.com:8080" 
     */
    domain: `http://${string}` | `https://${string}`;

    /**
     * 高级存储策略（可选）
     */
    advanced?: {
        /** 文件名生成策略 */
        filenameStrategy?: "timestamp" | "hash" | "original";
        /** 目录深度分级 */
        depthLevel?: number;
        /** 自动清理过期文件（单位：天） */
        autoPurgeDays?: number;
    };
}

/**
 * 阿里云OSS上传配置 
 * @desc 包含身份认证、存储桶配置及访问策略 
 * @link [OSS官方文档](https://help.aliyun.com/zh/oss) 
 */
interface AliyunOSSConfig {
    /**
     * 访问密钥ID 
     * @security Sensitive - 需加密存储 
     * @pattern 由阿里云颁发的LTAI开头的32位字符 
     * @example "LTAI5txxxxxxxxxxxxxxx"
     */
    accessKeyId: string;

    /**
     * 访问密钥凭证 
     * @security Sensitive - 需二次加密 
     * @note 泄漏将导致存储桶被恶意操控 
     */
    accessKeySecret: string;

    /**
     * 存储桶名称 
     * @rule 全局唯一且符合OSS命名规范 
     * @pattern 全小写字母、数字及连字符组合 
     * @example "madong-tech-prod"
     */
    bucket: string;

    /**
     * 存储目录路径 
     * @default "upload"
     * @rule 禁止包含../等路径穿越符号 
     */
    dirname: string;

    /**
     * 自定义访问域名 
     * @format 需包含协议头及CNAME解析 
     * @example "https://oss.madong.tech" 
     */
    domain: `http://${string}` | `https://${string}`;

    /**
     * 区域终端节点 
     * @enum 常用节点 
     * @example "oss-cn-hangzhou.aliyuncs.com"  - 杭州地域 
     * @example "oss-ap-southeast-1.aliyuncs.com"  - 新加坡地域 
     */
    endpoint: string;

    /**
     * 配置备注说明（可选）
     * @maxLength 200 - 控制台显示限制 
     */
    remark?: string;

    /**
     * 高级传输策略（可选）
     */
    advanced?: {
        /** 分片上传阈值（单位：MB） */
        multipartThreshold?: number;
        /** 并行上传线程数 */
        parallel?: number;
        /** 超时重试次数 */
        maxRetries?: number;
    };
}

/**
 * 腾讯云COS存储配置 
 * @desc 包含密钥管理、存储桶策略及访问控制 
 * @link [COS官方文档](https://cloud.tencent.com/document/product/436) 
 */
interface TencentCOSConfig {
    /**
     * 云API密钥ID 
     * @security Sensitive - 需加密存储 
     * @pattern 腾讯云标准的AKID开头32位字符 
     * @example "AKID5dxxxxxxxxxxxxxxxxxxxxxx"
     */
    secretId: string;

    /**
     * 云API密钥凭证 
     * @security Sensitive - 需二次加密 
     * @note 泄漏将导致存储桶数据泄露风险 
     */
    secretKey: string;

    /**
     * 存储桶名称 
     * @rule 命名格式为 [bucket]-[appid]
     * @example "webassets-1250000000"
     * @pattern ^[a-z0-9-]+-\d+$
     */
    bucket: string;

    /**
     * 存储目录路径 
     * @default "uploads"
     * @rule 禁止使用 ../ 等路径穿越符号 
     */
    dirname: string;

    /**
     * 自定义访问域名 
     * @format 需包含协议头及CDN加速配置 
     * @example "https://cos.madong.tech" 
     */
    domain: `http://${string}` | `https://${string}`;

    /**
     * 存储地域标识 
     * @enum ["ap-beijing", "ap-shanghai", "ap-guangzhou"]
     * @example "ap-guangzhou" - 广州地域 
     */
    region: string;

    /**
     * 配置描述信息（可选）
     * @maxLength 100 - 控制台显示限制 
     */
    remark?: string;

    /**
     * 高级传输配置（可选）
     */
    advanced?: {
        /** 分块上传阈值（单位：MB） */
        chunkSize?: number;
        /** 并发上传线程数 */
        concurrency?: number;
        /** 临时密钥有效期（秒） */
        tempKeyDuration?: number;
    };
}


/**
* 七牛云Kodo存储配置 
* @desc 包含访问凭证、存储区域策略及域名配置 
* @link [七牛云开发者文档](https://developer.qiniu.com/) 
*/
interface QiniuConfig {
    /**
     * 访问密钥（AK）
     * @security Sensitive - 需加密存储 
     * @pattern 七牛云标准的32位字符密钥 
     * @example "AK-5dxxxxxxxxxxxxxxxxxxxxxx"
     */
    accessKey: string;

    /**
     * 安全密钥（SK）
     * @security Sensitive - 需硬件加密模块保护 
     * @note 泄漏将导致存储桶完全失控 
     */
    secretKey: string;

    /**
     * 存储空间名称 
     * @rule 全局唯一且符合DNS命名规范 
     * @pattern ^[a-z0-9-]{3,63}$ - 全小写字母、数字、连字符 
     * @example "madong-prod-bucket"
     */
    bucket: string;

    /**
     * 存储目录路径 
     * @default "uploads"
     * @rule 禁止使用路径操作符（如../）
     */
    dirname: string;

    /**
     * 自定义加速域名 
     * @format 需完成CNAME解析的HTTPS域名 
     * @example "https://kodo.madong.tech" 
     */
    domain: `https://${string}`;

    /**
     * 存储区域标识 
     * @enum ["z0", "z1", "z2", "na0", "as0"]
     * @example "z0" - 华东区域 
     * @example "na0" - 北美区域 
     */
    region: string;

    /**
     * 配置备注（可选）
     * @maxLength 100 - 控制台显示限制 
     */
    remark?: string;

    /**
     * 高级上传策略（可选）
     */
    advanced?: {
        /** 分片上传阈值（单位：MB） */
        chunkSize?: 4 | 8 | 16;
        /** 直传/代理上传模式 */
        uploadMode?: "direct" | "proxy";
        /** 防盗链Token有效期（秒） */
        antiLeechTTL?: number;
    };
}

/**
* 亚马逊S3存储配置 
* @desc 包含IAM凭证、存储桶策略及跨区域复制配置 
* @link [AWS S3官方文档](https://docs.aws.amazon.com/s3/) 
*/
export interface AmazonS3Config {
    /**
     * IAM访问密钥ID 
     * @security Sensitive - 需KMS加密存储 
     * @pattern AWS标准的20位字母数字组合 
     * @example "AKIAIOSFODNN7EXAMPLE"
     */
    key: string;

    /**
     * IAM安全访问密钥 
     * @security Sensitive - 需硬件安全模块保护 
     * @note 泄漏将导致存储桶完全失控 
     */
    secret: string;

    /**
     * 存储桶名称 
     * @rule 全局唯一且符合DNS命名规范 
     * @pattern ^[a-z0-9.-]{3,63}$ - 全小写字母、数字、点、连字符 
     * @example "madong.enterprise.data" 
     */
    bucket: string;

    /**
     * 存储路径前缀 
     * @default "uploads"
     * @rule 禁止使用路径操作符（如../）
     */
    dirname: string;

    /**
     * 自定义访问域名 
     * @format 需完成CNAME解析的HTTPS域名 
     * @example "https://s3.madong.tech" 
     */
    domain: `https://${string}`;

    /**
     * 服务终端节点 
     * @enum ["s3.amazonaws.com",  "s3.us-east-2.amazonaws.com"] 
     * @example "s3.ap-southeast-1.amazonaws.com"  - 新加坡节点 
     */
    endpoint: string;

    /**
     * 存储区域标识 
     * @enum ["us-east-1", "eu-central-1", "ap-northeast-1"]
     * @example "ap-northeast-1" - 东京区域 
     */
    region: string;

    /**
     * 访问控制策略 
     * @enum ["private", "public-read", "bucket-owner-full-control"]
     * @default "private" - 默认私有读写 
     */
    acl: string;

    /**
     * 配置描述（可选）
     * @maxLength 200 - 控制台显示限制 
     */
    remark?: string;

    /**
     * 高级安全策略（可选）
     */
    advanced?: {
        /** 服务端加密算法 */
        encryption?: "AES256" | "aws:kms";
        /** 跨区域复制配置 */
        replication?: {
            destinationRegion: string;
            syncDelayMinutes: number;
        };
        /** 生命周期规则（天数） */
        lifecycleRules?: {
            transitionToGlacier?: number;
            expiration?: number;
        };
    };
}
