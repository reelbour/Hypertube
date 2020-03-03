#include <stdlib.h>
#include <unistd.h>
#include <stdio.h>
#include <fcntl.h>

typedef enum e_datatype
  {List, Dict}
datatype;

typedef enum e_keyval
  {First, Key, Val}
keyval;

typedef struct s_node {
  struct s_node *next;
  datatype dt;
  keyval kv;
} node;

node* add(datatype dt, node *list) {
  node* newnode;
  if (!(newnode = malloc(sizeof(node))))
    return NULL;

  newnode->next = list;
  newnode->dt = dt;
  newnode->kv = First;

  return newnode;
}

node* rm(node *list) {
  if (list == NULL)
    return NULL;

  node* res;
  res = list->next;
  free(list);

  return res;
}

void coma(node **state) {
  switch ((*state)->dt)
  {
    case Dict:
      switch ((*state)->kv)
      {
        case First:
          (*state)->kv = Key;
          break;

        case Key:
          (*state)->kv = Val;
          write(1, ":", 1);
          break;

        case Val:
          (*state)->kv = Key;
          write(1, ",", 1);
          break;
      }
      break;

    case List:
      if ((*state)->kv == First)
        (*state)->kv = Val;
      else
        write(1, ",", 1);
      break;
  }
}

int end(int ret, int fd, node** state) {
  while (*state) *state = rm(*state);
  close(fd);
  return ret;
}

int digest(char cbuf, int fd, node **state) {
  int res;

  switch (cbuf)
  {
    case 'd':
      if (*state) coma(state);
      write(1, "{", 1);

      *state = add(Dict, *state);

      break;

    case 'l':
      if (!*state) return end(20, fd, state);

      coma(state);
      write(1, "[", 1);

      *state = add(List, *state);

      break;

    case 'i':
      if (!*state) return end(30, fd, state);

      coma(state);

      res = read(fd, &cbuf, 1);
      if (res < 0) return end(31, fd, state);

      while (cbuf <= '9' && cbuf >= '0')
      {
        write(1, &cbuf, 1);

        if (res == 0) return end(32, fd, state);

        res = read(fd, &cbuf, 1);
        if (res < 0) return end(33, fd, state);
      }
      if (cbuf != 'e') return end(cbuf, fd, state);

      break;

    case 'e':
      if (!*state)
        return end(40, fd, state);
      if ((*state)->dt == Dict && (*state)->kv == Key)
        return end(41, fd, state);

      if ((*state)->dt == Dict)
        write(1, "}", 1);
      if ((*state)->dt == List)
        write(1, "]", 1);

      *state = rm(*state);

      break;

    default:
      if (!*state && cbuf == '\r') return end(0, fd, state);
      if (!*state) return end(50, fd, state);
      if (cbuf > '9' || cbuf < '0') return end(51, fd, state);

      int strlen = 0;

      while (cbuf <= '9' && cbuf >= '0')
      {
        strlen = (strlen * 10) + cbuf - '0';

        res = read(fd, &cbuf, 1);
        if (res < 0) return end(54, fd, state);
        if (res == 0) return end(53, fd, state);
      }
      if (cbuf != ':') return end(55, fd, state);

      char* sbuf = malloc(sizeof(char) * strlen);
      if (!sbuf) return end(56, fd, state);
      res = read(fd, sbuf, strlen);
      if (res < 0) return end(57, fd, state);
      if (res == 0 && *state) return end(58, fd, state);

      coma(state);
      write(1, "\"", 1);
      write(1, sbuf, strlen);
      write(1, "\"", 1);

      free(sbuf);
      break;
  }

  return 0;
}

int main(int argc, char const *argv[]) {
  if (argc != 2) return 1;

  char cbuf;
  int res, ores;
  node* state = NULL;
  int fd = open(argv[1], O_RDONLY);

  while ((res = read(fd, &cbuf, 1)))
  {
    if (res < 0) return end(2, fd, &state);

    ores = digest(cbuf, fd, &state);
    if (ores) return end(ores, fd, &state);
  }

  end(0, fd, &state);
}
