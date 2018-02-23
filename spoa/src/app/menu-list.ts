import { Menu } from './menu';

export const MenuList: Menu[] = [
  { id: 11, name: '一级菜单', keyword: 'job',
    childname: [
      {id: 111, name: '二级子菜单', keyword: 'job', },
      {id: 112, name: '二级子菜单', keyword: 'job', }
    ]
  },
  { id: 12, name: '一级菜单', keyword: 'employee',
  childname: [
    {id: 111, name: '二级子菜单', keyword: 'employee', },
    {id: 112, name: '二级子菜单', keyword: 'employee', },
    {id: 112, name: '二级子菜单', keyword: 'employee', }
  ]
  },
  { id: 13, name: '一级菜单', keyword: 'job', },
  { id: 14, name: '一级菜单', keyword: 'job', },
  { id: 15, name: '一级菜单', keyword: 'job', },
  { id: 16, name: '一级菜单', keyword: 'job', },
  { id: 17, name: '一级菜单', keyword: 'job', },
  { id: 18, name: '一级菜单', keyword: 'job', },
  { id: 19, name: '一级菜单', keyword: 'job', },
  { id: 20, name: '一级菜单', keyword: 'job', }
];
