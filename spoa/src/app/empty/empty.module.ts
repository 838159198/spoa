import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router'; // 路由
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

const ROUTES = [
];


@NgModule({
    declarations: [

    ],
    imports: [
      BrowserModule,
      FormsModule,
      HttpModule,
      RouterModule.forRoot(ROUTES),
    ],
    providers: [],
    bootstrap: []
  })
  export class EmptyModule { }
