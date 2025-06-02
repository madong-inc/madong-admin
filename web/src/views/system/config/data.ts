import { type FormProps } from '#/adapter';
import { $t } from '#/locale';

export const fieldLink = {
  root: '存储目录',
  secretId: 'secretId',
  accessKey: 'accessKey',
  accessKeyId: 'accessKeyId',
  secretKey: 'secretKey',
  key: 'key',
  accessKeySecret: 'accessKeySecret',
  secret: 'secret',
  bucket: '桶名称',
  dirname: '目录名称',
  domain: '域名地址',
  endpoint: 'Endpoint',
  region: '区域',
  acl: 'Acl',
  remark: '备注',
}

export const drawerFormSchamas: FormProps = {
  wrapperClass: 'grid-cols-12',
  commonConfig: {
    formItemClass: 'col-span-12',
    labelClass: 'mr-5',
  },
  schema: [
    {
      fieldName: 'mode',
      component: 'Input',
      dependencies: {
        show: () => false,
        triggerFields: [''],
      },
    },
    {
      fieldName: 'root',
      label: $t('system.config.upload.extend.root'),
      help: '',
      component: 'RadioGroup',
      rules: 'required',
      componentProps: {
        placeholder: '',
        allowClear: true,
        options: [
          {
            label: '不公开（runtime目录）',
            value: 'runtime',
          },
          {
            label: '公开（public目录）',
            value: 'public',
          },
        ],
      },
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['local'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'secretId',
      label: $t('system.config.upload.extend.secretId'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['cos'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'accessKey',
      label: $t('system.config.upload.extend.accessKey'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['qiniu'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'accessKeyId',
      label: $t('system.config.upload.extend.accessKeyId'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['oss'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'secretKey',
      label: $t('system.config.upload.extend.secretKey'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['cos', 'qiniu'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'key',
      label: $t('system.config.upload.extend.key'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['s3'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'accessKeySecret',
      label: $t('system.config.upload.extend.accessKeySecret'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['oss'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'secret',
      label: $t('system.config.upload.extend.secret'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['s3'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'bucket',
      label: $t('system.config.upload.extend.bucket'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['oss', 'cos', 'qiniu', 's3'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'dirname',
      label: $t('system.config.upload.extend.dirname'),
      rules: 'required',
    },
    {
      component: 'Input',
      fieldName: 'domain',
      label: $t('system.config.upload.extend.domain'),
      rules: 'required',
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['local', 'oss', 'cos', 'qiniu', 's3'].includes(values.mode);
        },
      },
    },

    {
      component: 'Input',
      fieldName: 'endpoint',
      label: $t('system.config.upload.extend.endpoint'),
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['oss', 'qiniu', 's3'].includes(values.mode);
        },
      },
    },

    {
      component: 'Input',
      fieldName: 'region',
      label: $t('system.config.upload.extend.region'),
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['cos', 's3'].includes(values.mode);
        },
      },
    },
    {
      component: 'Input',
      fieldName: 'acl',
      label: $t('system.config.upload.extend.acl'),
      dependencies: {
        triggerFields: ['mode'],
        if: (values) => {
          return ['s3'].includes(values.mode);
        },
      },
    },
    {
      component: 'Textarea',
      fieldName: 'remark',
      formItemClass: 'items-baseline',
       label: $t('system.config.upload.extend.remark'),
    },
  ],
};



/**
 * 构建配置数据
 * @param data
 * @param skipFields
 * @param extraFields
 */
export const transformData = function (
  data: { [s: string]: unknown } | ArrayLike<unknown>,
  skipFields: string[] = [],
  allowedKeys: string[] = [], // 添加允许的键集合
  extraFields: Record<string, string> = {},
  filedlink: Record<string, string> = {},
) {



  return Object.entries(data)
    .filter(([key]) =>
      !skipFields.includes(key) && (allowedKeys.length === 0 || allowedKeys.includes(key)) // 过滤跳过字段和不在允许集合中的字段
    )
    .map(([key, value]) => {
      const transformedObject = {
        code: key,
        content: value !== undefined ? value : "",
        name: filedlink[key],
      };

      Object.assign(transformedObject, extraFields);
      return transformedObject;
    });
}

/**
 * 返回模板字段
 * @param groupCode
 * @returns
 */
export const templateField = function (groupCode: string) {
  const data = {
    local: ['root', 'dirname', 'domain', 'remark'],
    oss: ['accessKeyId', 'accessKeySecret', 'bucket', 'domain', 'endpoint', 'dirname', 'remark'],
    cos: ['secretId', 'secretKey', 'bucket', 'domain', 'region', 'dirname', 'remark'],
    qiniu: ['accessKey', 'secretKey', 'bucket', 'domain', 'region', 'dirname', 'remark'],
    s3: ['key', 'secret', 'bucket', 'dirname', 'domain', 'region', 'version', 'endpoint', 'acl', 'remark'],
  }
  //@ts-ignore
  return data[groupCode] || [];
}

export const convertStringNumbers = (obj: Record<string, any>): Record<string, any> => {
  Object.keys(obj).forEach(key => {
    const value = obj[key];
    if (typeof value === 'string' && value.trim() !== "" && !isNaN(Number(value))) {
      obj[key] = Number(value);
    }
  });
  return obj;
};

