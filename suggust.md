第一种方式，即时搜索


第二种方式，Suggester


Suggester一共有四种：

Term suggester
Phrase Suggester
Completion Suggester
Context Suggester

Term suggester就是你输入把你的输入以一个个term的形式来分析，并返回你建议。就像上面的例子，接受用户输入的sabew，返回saber，属于term suggester。

Phrase Suggester更高级一点，他会以词组为单位返回建议。例如用户输入了noble prize，其实是拼错了，phrase suggester会返回nobel prize。

Completion Suggester提供了自动补全功能，但是设置挺复杂，这里不再展开。

Context Suggester是Completion Suggester的一个补充。Completion Suggester会去考虑index里面所有的documents。但是有时候，你想要先筛选掉一部分数据，或者是让某些字段重要性更高一点。这时候，可以使用Context Suggester。