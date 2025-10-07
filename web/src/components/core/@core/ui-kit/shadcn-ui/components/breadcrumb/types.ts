import type { Component } from 'vue';

import type { BreadcrumbStyleType } from '#/components/core/@core/typings';

export interface IBreadcrumb {
  icon?: Component | string;
  isHome?: boolean;
  items?: IBreadcrumb[];
  path?: string;
  title?: string;
}

export interface BreadcrumbProps {
  breadcrumbs: IBreadcrumb[];
  showIcon?: boolean;
  styleType?: BreadcrumbStyleType;
}
