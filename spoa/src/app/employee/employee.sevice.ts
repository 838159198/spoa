import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/toPromise';
@Injectable()
export class EmployeeService {
    headers: any;
    constructor(private http: Http) {
         this.headers = new Headers({ 'Content-type': 'application/json' });
     }
    // 获取数据
    getEmployees(url: any) {
        return this.http.get(url)
            .toPromise()
            .then(res => res.json())
            .catch(this.handleError);
    }

    private handleError(error: any): Promise<any> {
        console.error('An error occurred', error); // for demo purposes only
        return Promise.reject(error.message || error);
    }

    // 新建post数据
    create(url: string, hero: any) {
        return this.http
            .post(url, JSON.stringify(hero), { headers: this.headers })
            .toPromise()
            .then(response => response.json())
            .catch(this.handleError);
    }
    // 更新数据update
    update(url: string, hero: any) {
        return this.http
               .put(url, JSON.stringify(hero), {headers: this.headers})
               .toPromise()
               .then(response => response.json())
               .catch(this.handleError);
    }
    // 删除数据 delete
    delete(url: string) {
        return this.http
              .delete(url, {headers: this.headers})
              .toPromise()
              .then(response => response.json())
              .catch(this.handleError);
    }
}
