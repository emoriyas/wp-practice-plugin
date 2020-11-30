# Practice Plugin

This is a simple WordPress plugin that inserts HTML codes to a post. Upon activation, this plugin will create a database table named _[prefix]wppp_html_entry_ and a new admin settings page named _HTML Entry_ will be created.

Each HTML entry can be set to be included on certain posts based on catagories, tags, and the number of paragraphs.

##### Admin Page Settings:
* __HTML:__ HTML code to be inserted into posts.
* __Paragraph Greater/Fewer:__ HTML will not be inserted with posts greater or fewer than the specified number of paragraphs.
* __Paragraph Limit:__ Paragraph limit for the posts.
* __Categories:__ HTML will be inserted to posts with these categories.
* __Categories Excluded:__ HTML will not be inserted to posts with these categories.
* __Tags:__ HTML will be inserted to posts with these tags.
* __Tags Excluded:__ HTML will not be inserted to posts with these tags.
* __Insertion Type:__ Determines if the HTML entry will be entered at the first paragraph, last paragraph, or iteratively.
* __Insertion After/Before:__ Determines if the HTML entry will be inserted after or before the targer paragraphs.
* __Insertion Every:__ Only applicable if _Insertion Type_ is set to _iterative_. Inserts HTML entry iteratively, skipping over the specified number of paragraphs.
* __Active:__ Determines if HTML entry should be inserted.

The current admin page is faulty and will not be inserting data. In order for this pluggin to work, data must be manually entered in the database.

Following is a sample insertion query used during testing.

```
INSERT INTO wp_wppp_html_entry
    (html, paragraph_limit_greater, paragraph_limit_value,
    tags_included, tags_excluded, categories_included,
    categories_excluded, is_active, insert_condition_type,
    insert_condition_after, insert_condition_value)
VALUES ('<p>Hello World!</p>', null, null, 'tag1, tag3, tag4',
    'tag2', 'category1, category2', null, true, 'iterate', true, 1),
    ('<p>Entry 1</p>', null, null, null, null, null, null, 1, null, null, null),
    ('<p>Entry 2</p>', true, 5, 'tag1', null, null, null, 1, 'first', false, null),
    ('<p>Entry 3</p>', false, 5, null, null, 'category1', null, 1, null, null, null);
```

