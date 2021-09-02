import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CookieLawComponent } from './cookie-law.component';

describe('CookieLawComponent', () => {
  let component: CookieLawComponent;
  let fixture: ComponentFixture<CookieLawComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CookieLawComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(CookieLawComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
