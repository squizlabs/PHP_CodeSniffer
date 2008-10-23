one = (1 + 2);
two = (one + 2);
two = (one + one);
three = ('1' + 2);

four = ['1', two].join();
four = ['1', two].join('');
four = ['1', [one, two].join(',')].join(' ');
four = ['1', [one, two].join()].join(' ');
four = ['1', [one, two].join()].join();

five = 'string' + ['1', [one, two].join()].join() + 'string';

six = myArray.join(' ');
six = [arrayOne, arrayTwo].join();