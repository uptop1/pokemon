Pokemon RPG
===========

A simple RPG simulates Pokemon training adventure. It's a text based one runs on the console developed in PHP with symfony 3 framework. It's ready to be implemented in API or as a web application by calling PokemonGame class.

Hope you are going to enjoy the game as much I enjoyed building it.

Demo
---------------
![gif](http://i.imgur.com/SgnBl5z.gif)

Features
---------------
1. Start new game
2. Pick your first pokemon
3. Display your current info and your pokemon stats
4. Visit places
5. Start battles with wild pokemons
6. Save the game
7. Load saved game

Tools used
---------------
1. PHP7
2. Symfony3
3. Doctrine
4. MySQL DB
5. PHPUnit

Which files I built exactly
---------------
1. PokemonGame at `src/Pokemon/PokemonGame.php`
2. PlayCommand at `src/PokemonBundle/Command/PlayCommand.php`
3. Console application initiation at `bin/pokemon`
4. Imported entities at `src/PokemonBundle/Entity/`
5. Tests at `tests/Pokemon/PokemonGameTest.php`

Getting Started
---------------

### Installation
1. Clone the repository `git clone https://github.com/uptop1/pokemon.git`
2. Change directory `cd pokemon`
3. Install the libraries using composer `composer install`
4. Create a database
5. Import `db/pokemon.sql` file into your newly created database
6. Copy `/app/config/parameters.yml.dist` to `/app/config/parameters.yml`
7. Set database connection parameters in `/app/config/parameters.yml`

Usage
-----

Use php CLI to run the console command:

### Start new game
```bash
$ php bin/pokemon
```

### Resume saved game
```bash
$ php bin/pokemon play [username]
```

Note
---------------
I do not usually use symfony framework nor doctrine, but I thought it's a great chance to try them so I built this game and I fell in love with symfony :D. If you find any issue, please inform me, I really appretiate feedbacks.