import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { from } from 'rxjs';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Task } from '../../models/task';
import { TaskService } from '../../services/task.service';
import { List } from '../../models/list';
import { ListService } from '../../services/list.service';
import { global } from '../../services/global.service';
import { ThisReceiver } from '@angular/compiler';
import { MatExpansionModule } from '@angular/material/expansion';
import { browser } from 'protractor';
import { THIS_EXPR } from '@angular/compiler/src/output/output_ast';
import { features } from 'node:process';
import { CdkDragDrop, moveItemInArray } from '@angular/cdk/drag-drop';

@Component({
  selector: 'home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css'],
  providers: [UserService, TaskService, ListService]
})

export class HomeComponent implements OnInit {

  public user: User;
  public identity;
  public token;
  public status: string;
  public id;

  public list: List;
  public lists: Array<List>;
  public _lists;

  public task: Task;
  public tasks: Array<Task>;
  public tasksOfList;

  public loading: boolean;

  panelOpenState = false;

  constructor(
    private _userService: UserService,
    private _listService: ListService,
    private _taskService: TaskService,
    private _router: Router,
    private _route: ActivatedRoute,
    private rest: UserService,

  ) {
    this.list = new List(null, null, null);
    this.task = new Task(null, null, null, null, null, null, null);
    this.user = new User(1, '', '', '', '', '', '');
    this.token = this._userService.getToken();
    this.identity = this._userService.getIdentity();

    this.user = new User(
      this.identity.sub,
      this.identity.name,
      this.identity.email, '',
      this.identity.image,
      this.identity.task_list,
      this.identity.newpassword,
    );

    if (!this.user) {
      localStorage.setItem('cover', 'true');
    }
  }

  ngOnInit(): void {
    if (!this.user) {
      localStorage.setItem('cover', 'false');
    }
    this.loadUser();
    this.readerList();
  }

  addTask(data){
    this.task= data
    let task_list = localStorage.getItem('task-list');
    let newtasks=JSON.stringify(this.task);
    task_list=task_list.substring(0, task_list.length-1);
    task_list=task_list.concat( ',' + newtasks + ']');
    task_list=JSON.parse(task_list);

    this.user.tasks_list=task_list;

    this._userService.update(this.token, this.user).subscribe(
      response => {
        if (response && response.status) {
          this.status = 'success';

          if (response.change.tasks_list) {
            this.user.tasks_list = response.change.tasks_list;
          }
          this.identity = this.user;
          localStorage.setItem('identity', JSON.stringify(this.identity));

          localStorage.setItem('task-list',JSON.stringify(this.user.tasks_list));

        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );

  }

  loadUser() {
    this.user = this._userService.getIdentity();
  }



  drop(event: CdkDragDrop<string[]>) {
    moveItemInArray(this.tasks, event.previousIndex, event.currentIndex);

    let tasks = JSON.stringify(this.tasks);
    this.user.tasks_list = tasks;

    this._userService.update(this.token, this.user).subscribe(
      response => {
        if (response && response.status) {
          this.status = 'success';

          if (response.change.tasks_list) {
            this.user.tasks_list = response.change.tasks_list;
          }
          this.identity = this.user;
          localStorage.setItem('identity', JSON.stringify(this.identity));
          localStorage.setItem('task-list', this.user.tasks_list);

        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );
  }

  //LIST////////////////////////////////////////////

  createList(form) {
    this._listService.create(this.token, this.list).subscribe(
      response => {
        if (response.status = 'success') {
          this.list = response.list;
          this.status = 'success';

        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

  readerList() {
    this.loading = true;
    this._listService.getList(this.token, this.identity).subscribe(
      response => {
        this.lists = response;
        this.status = "success";
        this.loading = false;
        this.identity = this.user.id

        this._taskService.getList(this.token, this.identity).subscribe(
          response => {
            this.tasks = response;
            this.status = "success";
            this.tasks=JSON.parse(this.user.tasks_list);
            localStorage.setItem('task-list', this.user.tasks_list);
          }, error => {
            console.error(error);
            this.status = "error";
          }
        )
      }, error => {
        console.error(error);
        this.status = "error";
      }
    )
  }

  onLoading() {
    this.loading = true;
  }

  ofLoading() {
    this.loading = false;
  }

  updateList(form) {
     ///usar metodo slice   mivariable.splice(desde donde cuenta(0), cuantos cambia(1), lo que hay que cambiar)
    this._listService.update(this.token, form);
    this.loading = false;
  }

  deleteList(form) {
    ///usar metodo slice   mivariable.splice(desde donde cuenta(0), cuantos elimina(1))
    this._listService.delete(this.token, form);
    this.loading = false;
  }

  //TASK////////////////////////////////////////////

  getVal(id) {
    this.id = id;
  }

  createTask(form) {
    this.task.list_id = this.id;
    this._taskService.create(this.token, this.task).subscribe(
      response => {
        if (response.status = 'success') {
          this.task = response.task;
          this.status = 'success';
          this.readerList();
          this.loading = false;
          this.addTask(this.task);
        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )
  }

  localStorageItem(id: string): string {
    return localStorage.getItem(id);
  }

  updateTask(form) {
    this._taskService.update(this.token, form);
  }

  deleteTask(form) {
    this._taskService.delete(this.token, form);
  }
}








