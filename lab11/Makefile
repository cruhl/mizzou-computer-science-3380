CFLAGS = -Wall -O2
LIBS = -lsqlite3

all: dump load

dump: dump.o
	g++ $(CFLAGS) -o $@ $< $(LIBS)

load: load.o
	g++ $(CFLAGS) -o $@ $< $(LIBS)

%.o: %.cpp
	g++ $(CFLAGS) -c $< 

.PHONY: clean
clean:
	rm -f dump load *.o *~
