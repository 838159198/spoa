import { Injectable } from '@angular/core';
import { MenuList } from './menu-list';

@Injectable()
export class MenuService {
    getMenuList() {
        return MenuList;
    }
}
