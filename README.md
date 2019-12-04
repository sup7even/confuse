# ConfUse 

a (maybe) smarter way to write your TCA

## what it supposed to be

- write your TCA files with autocomplete
- maintain your TCA in a global file

## what it is not supposed to be

- a well documented project by now
- a complete extension due to lack of time

---

## use it like this in your TCA (overrides)

```
\Supseven\Confuse\Service\Tca\Create::getInstance()->addElement([
    \Supseven\Confuse\Service\Tca\Elements\Check::create()
        ->setName('test')
        ->setExclude(true)
        ->setValue([
            ['', ''],
            ['.h1', 'h1'],
            ['.h2', 'h2'],
        ])
        ->build()
])->persist('tt_content');

```