# CloudLib :: Lightweight RESTful MVC PHP Framework

> CloudLib is a frameworked developed mainly for learning purposes,
> by doing so it tries to conform to the constraints of REST and to
> a certain degree the MVC design pattern.

---

### TODOs (*mainly to keep ideas in writing*)
+ Combine the View class into the Response class?
+ Add a Route class? The Router class seems ambigious tbh.
+ Create a wrapper class for the system startup?
+ Add some sort of Dependency Injection Container
+ Add more flexibility to the directory structure for the developer?
+ Add some sort of File class to handle simple operations as finding a file?
(tho, don't want to make classes depend on another class in this way)
+ Make developer able to create a View inside a View (and so on) to create a subset template layer system?
+ Unit testing?
+ Profiling?
+ **PSR-0** standard? Implement some sort of a autoloader? Would it work for classes being accessed in Views,
would it be more of a hassel to use them? (tho it might be worth it due to the compability with other vendor libraries)
+ Being able to Log to different files?
+ Session tokens?
+ Protection against XSS, XSRF.

### Fix!
1. Remake old Image class
2. Remake old Uploader class

