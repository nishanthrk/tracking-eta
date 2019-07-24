<p align="center">
    <h1 align="center">Location Tracking & ETA System</h1>
    <br>
</p>

Requirement :
<ul>
    <li> php 7.0+
    <li> redis-cli
</ul>

Execution Commands :

<ul>
    <li> composer install
    <li> php -S localhost:8000 -t tracking-eta/
</ul>

<h3>Screenshots</h3>
Landing Page
![Screenshot](https://i.ibb.co/n6VTNXz/Screenshot-2019-07-24-at-7-58-21-AM.png)
Source and Destination
![Screenshot](https://i.ibb.co/2WrK4Vj/Screenshot-2019-07-24-at-7-58-37-AM.png)
Movement
![Screenshot](https://i.ibb.co/z8R907r/Screenshot-2019-07-24-at-7-59-19-AM.png)


DIRECTORY STRUCTURE
-------------------

```
api/                     the code of all the api
config/                  config and utility file
vendor/                  contains dependent 3rd-party packages
index.php                index page with map
main.js                  all js funcationality
```
