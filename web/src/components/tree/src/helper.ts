import type { MenuPermissionOption } from './data';

import type { useVxeGrid } from '#/adapter/vxe-table';
import type { SystemMenuRow } from '#/api/system/menu';

import { eachTree, treeToList } from '#/utils';

//@ts-ignore
import { difference, isEmpty, isUndefined } from 'lodash-es';

/**
 * 权限列设置是否全选
 * @param record 行记录
 * @param checked 是否选中
 */
export function setPermissionsChecked(
  record: MenuPermissionOption,
  checked: boolean,
) {
  if (record?.permissions?.length > 0) {
    // 全部设置为选中
    record.permissions.forEach((permission) => {
      permission.checked = checked;
    });
  }
}

/**
 * 设置当前行 & 所有子节点选中状态
 * @param record 行
 * @param checked 是否选中
 */
export function rowAndChildrenChecked(
  record: MenuPermissionOption,
  checked: boolean,
) {
  // 当前行选中
  setPermissionsChecked(record, checked);
  // 所有子节点选中
  record?.children?.forEach?.((permission) => {
    rowAndChildrenChecked(permission as MenuPermissionOption, checked);
  });
}

/**
 * void方法 会直接修改原始数据
 * 将树结构转为 tree+permissions结构
 * @param menus 后台返回的menu
 */
export function menusWithPermissions(menus: SystemMenuRow[]) {
  eachTree(menus, (item: MenuPermissionOption) => {
    if (item.children && item.children.length > 0) {
      /**
       * 所有为api的节点提取出来
       * 需要注意 这里需要过滤目录下直接是按钮的情况
       * 将按钮往children添加而非加到permissions
       */
      const permissions = item.children.filter(
        (child: SystemMenuRow) => child.type === 4);
      // 取差集
      const diffCollection = difference(item.children, permissions);
      // 更新后的children  即去除按钮
      item.children = diffCollection;

      // permissions作为字段添加到item
      const permissionsArr = permissions.map((permission) => {
        return {
          id: permission.id,
          title: permission.title,
          checked: false,
        };
      });
      item.permissions = permissionsArr;
    }
  });
}

/**
 * 设置表格选中
 * @param checkedKeys 选中的keys
 * @param menus 菜单 转换后的菜单
 * @param tableApi api
 * @param association 是否节点关联
 */
export function setTableChecked(
  checkedKeys: (number | string)[],
  menus: MenuPermissionOption[],
  tableApi: ReturnType<typeof useVxeGrid>['1'],
  association: boolean,
) {
  // tree转list
  const menuList: MenuPermissionOption[] = treeToList(menus);
  // 拿到勾选的行数据
  let checkedRows = menuList.filter((item) => checkedKeys.includes(item.id));

  /**
   * 节点独立切换到节点关联 只需要最末尾的数据 即children为空
   */
  if (!association) {
    checkedRows = checkedRows.filter(
      (item) => isUndefined(item.children) || isEmpty(item.children),
    );
  }

  // 设置行选中 & permissions选中
  checkedRows.forEach((item) => {
    tableApi.grid.setCheckboxRow(item, true);
    if (item?.permissions?.length > 0) {
      item.permissions.forEach((permission) => {
        if (checkedKeys.includes(permission.id)) {
          permission.checked = true;
        }
      });
    }
  });

  /**
   * 节点独立切换到节点关联
   * 勾选后还需要过滤权限没有任何勾选的情况 这时候取消行的勾选
   */
  if (!association) {
    const emptyRows = checkedRows.filter((item) => {
      if (isUndefined(item.permissions) || isEmpty(item.permissions)) {
        return false;
      }
      return item.permissions.every(
        (permission) => permission.checked === false,
      );
    });
    // 设置为不选中
    tableApi.grid.setCheckboxRow(emptyRows, false);
  }
}
