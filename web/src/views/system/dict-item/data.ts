import type { FormSchema, OnActionClickFn, VxeTableGridOptions } from '#/adapter';
import type { SystemDictItemRow } from '#/api/system/dict';
import { DictEnum } from '#/components/common/constants';
import { $t } from '#/locale';
import { getDictOptions } from '#/utils';
import { Tag } from 'ant-design-vue';
import { h } from 'vue';




/**
 * 
 * @param onActionClick 
 * @param onStatusChange 
 * @returns 
 */
export function useColumns(
  onActionClick: OnActionClickFn<SystemDictItemRow>,
  onStatusChange?: (newStatus: any, row: SystemDictItemRow) => PromiseLike<boolean | undefined>,
): VxeTableGridOptions<SystemDictItemRow>['columns'] {
  return [
    {
      type: 'checkbox',
      width: 60
    },
    {
      field: 'code',
      title: $t('system.dict_item.list.table.columns.code'),
      width: 150,
      visible: false
    },

    {
      field: 'label',
      title: $t('system.dict_item.list.table.columns.label'),
      minWidth: 120,
      slots: {
        default: ({ row }) => {
          const isValidColor = (
            typeof row.color === 'string' &&
            row.color.trim() !== '' &&
            row.color !== 'null' &&
            row.color !== 'undefined'
          );

          return isValidColor
            ? h(Tag, { color: row.color }, row.label)
            : row.label;
        }
      }
    },
    {
      field: 'value',
      title: $t('system.dict_item.list.table.columns.value'),
      minWidth: 90
    },
    {
      field: 'sort',
      title: $t('system.dict_item.list.table.columns.sort'),
       minWidth: 90
    },
    {
      field: 'enabled',
      title: $t('system.dict_item.list.table.columns.enabled'),
      cellRender: {
        name: 'CellTag',
      },
      visible: false
    },
    {
      field: 'remark',
      title: $t('system.dict_item.list.table.columns.remark'),
      visible:false
    },
    {
      field: 'created_date',
      title: $t('system.dict_item.list.table.columns.created_date'),
      width: 170
    },
    {
      field: 'updated_date',
      title: $t('system.dict_item.list.table.columns.updated_date'),
      width: 170,
      visible: false
    },
    {
      align: 'center',
      cellRender: {
        name: 'CellOperation',
        attrs: {
          nameField: 'name',
          onClick: onActionClick,
        },
        options: [
          {
            code: 'edit',
            text: $t('system.dict_item.list.table.columns.actions.edit'),
            auth: ['admin', 'system:dict_item:edit'],
          },
          {
            code: 'delete',
            text: $t('system.dict_item.list.table.columns.actions.delete.label'),
            auth: ['admin', 'system:dict_item:delete'],
          }
        ],
      },
      field: 'operation',
      headerAlign: 'center',
      showOverflow: false,
      title: $t('system.dict_item.list.table.columns.operation'),
      width: 180,
    }
  ]
}




// 搜索表单参数定义
export function querySchema(): FormSchema[] {
  return [
    {
      component: 'Input',
      fieldName: 'LIKE_label',
      label: $t('system.dict_item.list.query.label'),
      componentProps: {
        placeholder: $t('system.dict_item.list.query.placeholder.label'),
        allowClear: true,
      },
    },
    {
      component: 'Input',
      fieldName: 'LIKE_value',
      label: $t('system.dict_item.list.query.value'),
      componentProps: {
        placeholder: $t('system.dict_item.list.query.placeholder.value'),
        allowClear: true,
      },
    },
    {
      fieldName: 'EQ_enabled',
      label: $t('system.dict_item.list.query.enabled'),
      component: 'Select',
      componentProps: {
        allowClear: true,
        options: getDictOptions(DictEnum.SYS_YES_NO),
        placeholder: $t('system.dict_item.list.query.placeholder.enabled'),
      },
    },
  ]
};


// 表单参数定义
export function formSchamas(): FormSchema[] {
  return [
    {
      fieldName: 'id',
      label: $t('system.dict_item.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['id'],
      },
    },
    {
      fieldName: 'dict_id',
      label: $t('system.dict_item.form.modal.id'),
      component: 'Input',
      dependencies: {
        show: false,
        triggerFields: ['dict_id'],
      },
    },
    {
      component: 'Input',
      dependencies: {
        triggerFields: ['code'],
        disabled: true,
      },
      componentProps: {
        class: 'w-full'
      },
      fieldName: 'code',
      label: $t('system.dict_item.form.modal.code'),
    },
    {
      component: 'Input',
      fieldName: 'color',
      label: $t('system.dict_item.form.modal.color'),
    },
    {
      fieldName: 'label',
      label: $t('system.dict_item.form.modal.label'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.dict_item.form.modal.placeholder.label'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      fieldName: 'value',
      label: $t('system.dict_item.form.modal.value'),
      component: 'Input',
      componentProps: {
        placeholder: $t('system.dict_item.form.modal.placeholder.value'),
        allowClear: true,
      },
      rules: 'required',
    },
    {
      component: 'Textarea',
      componentProps: {
        placeholder: $t('system.dict_item.form.modal.placeholder.other_class'),
      },
      fieldName: 'other_class',
      formItemClass: 'items-start',
      help: $t('system.dict_item.form.modal.help.other_class'),
      label: $t('system.dict_item.form.modal.other_class'),
    },
    {
      fieldName: 'enabled',
      label: $t('system.dict_item.form.modal.enabled'),
      component: 'RadioGroup',
      defaultValue: 1,
      componentProps: {
        optionType: 'button',
        buttonStyle: 'solid',
        options: getDictOptions(DictEnum.SYS_YES_NO)
      },
      rules: 'selectRequired',
      dependencies: {
        triggerFields: ['enabled'],
        show: false
      }
    },
    {
      fieldName: 'sort',
      label: $t('system.dict_item.form.modal.sort'),
      component: 'InputNumber',
      defaultValue: 0,
      componentProps: {
        placeholder: $t('system.dict_item.form.modal.placeholder.sort'),
        allowClear: true,
      },
    },
    {
      fieldName: 'remark',
      label: $t('system.dict_item.form.modal.remark'),
      component: 'Textarea',
      componentProps: {
        placeholder: $t('system.dict_item.form.modal.placeholder.remark'),
      },
    },
  ]
};
