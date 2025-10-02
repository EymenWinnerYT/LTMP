**LTMP (Lightweight Text Markup Processor)** is a markup language. It uses tag system, and it allows nesting in under 200 lines of code. Tag syntax is like `[TAG text]` or `[TAG arg1 text]` or `[TAG arg1 arg2 text]`.

## How to include in PHP

```php
require "ltmp.php";
$ltmp = new LTMP();
$parsed_text = $ltmp->parse("This is a [BOLD bold] text.");
if($ltmp->parse_error){
  die("Failed to parse! Please check for typos.");
}
echo $parsed_text;
```

## Tags list
`BOLD`
- Marks text as bold. Example: This is `[BOLD bold]` text.
- Output: This is **bold** text.

`ITALIC`
- MArks text as italic. Example: This is `[ITALIC italic]` text.
- Output: This is *italic* text.

`INCODE`
- Marks a code. One-liner by default. Example: `[INCODE This is my code.]`
- Output: `This is my code.`

`CODE`
- Marks a code. Supports multiple lines. Example: `[CODE This is my codeblock.]`
- Output: ```This is my codeblock.```

`TCOLOR`:
- Displays a text with colors. Syntax: `[TCOLOR bg fg]` (bg and fg are hex like FF00AA)
- Example: `[TCOLOR FF0000 FFFFFF This is a text with red background and white text color.]`

`LINK`
- Displays a link. Syntax: `[LINK URL text]`
- Example: `[LINK https://google.com Google website]`
- Renders as: [Google website](https://google.com)

`LIST`
- Creates an unordered list.

`OLIST`
- Creates an ordered list with numbers.

`LISTI`
- Creates a list item. Nested in `LIST` or `OLIST`.
Example: `[LIST [LISTI Apple][LISTI Banana][LISTI Cherry]]`
- Renders as:
- Apple
- Banana
- Cherry
- OR:
`[OLIST [LISTI Apple][LISTI Banana][LISTI Cherry]]`
- Renders as:
1. Apple
2. Banana
3. Cherry

`HEADER1-6`
- These are tags creates headers. Example: ```[HEADER1 Title]
[HEADER2 Subtitle]```
- Renders as:
# Title
## Subtitle

`EXP`
- Exponent text/superscript. Example: `2[EXP 3]`
- Renders as: 2<sup>3</sup>

`IMAGE`
- Displays an image with URL. Example `[IMAGE http://eymenwinneryt.42web.io/banner1.gif]`
- Renders as: [](http://eymenwinneryt.42web.io/banner1.gif)

`CLT` (Chemical low text)
- Subscript. Example: `H[CLT 2]SO[CLT 4]`
- Renders as: H<sub>2</sub>SO<sub>4</sup>

`!`
- Comment tag, ignored by parser. Example: ```[! this is my paragraph]
[HEADER1 Lorem Ipsum]```

`DEFVAR`
- Defines a variable or array of strings. Whitespace sensitive. Examples:
- ```
  [DEFVAR version 1.0] [! defines a single variable]
  [DEFVAR fruits Apple,Banana,Cherry] [! defines an array]
  ```

`DISPVAR`
- Displays a variable or array item. Array items are indexed using `array.item` syntax. Arrays are 0-indexed.
- Examples:
- ```
  [DISPVAR version] [! displays "1.0"]
  [DISPVAR fruits.0] [! displays "Apple"]
  [DISPVAR fruits.2] [! displays "Cherry"]
  ```
